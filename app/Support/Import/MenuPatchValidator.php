<?php

namespace App\Support\Import;

use App\Models\Item;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\User;
use App\Models\RestaurantSocialLink;

use App\Support\Permissions;

use Illuminate\Support\Str;


class MenuPatchValidator
{
    public function validate(array $payload, User $user, Restaurant $restaurant): array
        {
            $errors=[];
            $plan=['dry_run'=>false,'ops'=>[],'summary'=>['create'=>0,'update'=>0,'delete'=>0]];
            Permissions::abortUnless($user,'import.menu_json');

            if (($payload['mode']??null)!=='patch') {
                $errors[]=['path'=>'mode','message_key'=>'admin.import.errors.mode_invalid','params'=>[]];
                return compact('errors','plan');
            }

            foreach ($payload['operations']??[] as $op) {
                if ($op['type']==='item' && $op['op']==='upsert') {
                    $plan['ops'][]=[
                        'type'=>'item',
                        'op'=>'create',
                        'key'=>$op['key'],
                        'parent'=>$op['parent'],
                        'set'=>$op['set']
                    ];
                    $plan['summary']['create']++;
                }
            }
            return compact('errors','plan');
        }


    /**
     * Normalize, sanitize, validate fields in item.set.
     * Writes errors as message_key+params; NO raw text.
     */
    private function normalizeItemSet(array $set, User $user, array &$errors, string $base): array
    {
        $out = [];

        $cleanStr = function ($v): ?string {
            if (!is_string($v)) return null;
            $v = trim(strip_tags($v));
            $v = preg_replace('/\s+/u', ' ', $v);
            return $v === '' ? null : $v;
        };

        // price (decimal 8,2)
        if (array_key_exists('price', $set)) {
            $raw = $set['price'];
            $p = (is_numeric($raw) || is_string($raw)) ? trim((string)$raw) : null;

            if ($p === null || !preg_match('/^\d{1,6}(\.\d{1,2})?$/', $p)) {
                $errors[] = $this->err("$base.set.price", 'admin.import.errors.price_invalid', []);
            } else {
                $out['price'] = $p;
            }
        }

        // currency (optional) - currently only EUR allowed
        if (array_key_exists('currency', $set)) {
            $c = $cleanStr($set['currency']);
            if ($c === null || !in_array($c, ['EUR'], true)) {
                $errors[] = $this->err("$base.set.currency", 'admin.import.errors.currency_invalid', []);
            } else {
                $out['currency'] = $c;
            }
        }

        // is_active
        if (array_key_exists('is_active', $set)) {
            Permissions::abortUnless($user, 'items.toggle.active');
            if (!is_bool($set['is_active'])) {
                $errors[] = $this->err("$base.set.is_active", 'admin.import.errors.boolean_required', []);
            } else {
                $out['is_active'] = $set['is_active'];
            }
        }

        // image (relative path) - allow null to clear image
        if (array_key_exists('image', $set)) {
            $img = $set['image'];
            if ($img === null) {
                $out['image'] = null;
            } else {
                $img = $cleanStr($img);
                if ($img === null) {
                    $errors[] = $this->err("$base.set.image", 'admin.import.errors.image_path_invalid', []);
                } else {
                    if ($this->isUnsafePath($img)) {
                        $errors[] = $this->err("$base.set.image", 'admin.import.errors.path_unsafe', []);
                    } else {
                        $out['image'] = $img;
                    }
                }
            }
        }

        // meta flags
        if (array_key_exists('meta', $set)) {
            if (!is_array($set['meta'])) {
                $errors[] = $this->err("$base.set.meta", 'admin.import.errors.meta_object_required', []);
            } else {
                $m = $set['meta'];
                $meta = [];

                if (array_key_exists('show_image', $m)) {
                    Permissions::abortUnless($user, 'items.toggle.show_image');
                    if (!is_bool($m['show_image'])) $errors[] = $this->err("$base.set.meta.show_image", 'admin.import.errors.boolean_required', []);
                    else $meta['show_image'] = $m['show_image'];
                }

                if (array_key_exists('is_new', $m)) {
                    Permissions::abortUnless($user, 'items.flag.new');
                    if (!is_bool($m['is_new'])) $errors[] = $this->err("$base.set.meta.is_new", 'admin.import.errors.boolean_required', []);
                    else $meta['is_new'] = $m['is_new'];
                }

                if (array_key_exists('dish_of_day', $m)) {
                    Permissions::abortUnless($user, 'items.flag.dish_of_day');
                    if (!is_bool($m['dish_of_day'])) $errors[] = $this->err("$base.set.meta.dish_of_day", 'admin.import.errors.boolean_required', []);
                    else $meta['dish_of_day'] = $m['dish_of_day'];
                }

                if (array_key_exists('spicy', $m)) {
                    Permissions::abortUnless($user, 'items.flag.spicy');
                    if (!is_int($m['spicy'])) $errors[] = $this->err("$base.set.meta.spicy", 'admin.import.errors.spicy_invalid', []);
                    elseif ($m['spicy'] < 0 || $m['spicy'] > 3) $errors[] = $this->err("$base.set.meta.spicy", 'admin.import.errors.spicy_invalid', []);
                    else $meta['spicy'] = $m['spicy'];
                }

                if (!empty($meta)) {
                    $out['meta'] = $meta;
                }
            }
        }

        // translations
        if (array_key_exists('translations', $set)) {
            if (!is_array($set['translations'])) {
                $errors[] = $this->err("$base.set.translations", 'admin.import.errors.translations_object_required', []);
            } else {
                $trOut = [];
                foreach ($set['translations'] as $locale => $tr) {
                    $locPath = "$base.set.translations.$locale";

                    if (!in_array($locale, ['de', 'en', 'ru'], true)) {
                        $errors[] = $this->err($locPath, 'admin.import.errors.locale_not_supported', ['locale' => (string)$locale]);
                        continue;
                    }
                    if (!is_array($tr)) {
                        $errors[] = $this->err($locPath, 'admin.import.errors.translation_object_required', []);
                        continue;
                    }

                    $row = [];
                    foreach (['title', 'description', 'details'] as $field) {
                        if (array_key_exists($field, $tr)) {
                            $row[$field] = $cleanStr($tr[$field]);
                        }
                    }

                    // At least something must be provided per locale
                    if (!empty($row)) {
                        $trOut[$locale] = $row;
                    } else {
                        $errors[] = $this->err($locPath, 'admin.import.errors.translation_empty', []);
                    }
                }

                if (!empty($trOut)) {
                    $out['translations'] = $trOut;
                }
            }
        }

        // set cannot be empty for update/upsert
        if (empty($out)) {
            $errors[] = $this->err("$base.set", 'admin.import.errors.set_empty', []);
        }

        return $out;
    }

    /**
     * Resolve "pretty parent" (category_key + subcategory_key) to concrete Section.
     * - category: section with parent_id null
     * - subcategory: section with parent_id = category.id
     */
    private function resolveParentSection(Restaurant $restaurant, string $categoryKey, ?string $subcategoryKey): ?Section
    {
        $cat = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->where('key', $categoryKey)
            ->first();

        if (!$cat) return null;

        if ($subcategoryKey === null || $subcategoryKey === '') {
            return $cat;
        }

        return Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('parent_id', $cat->id)
            ->where('key', $subcategoryKey)
            ->first();
    }

    /**
     * Find item by key within restaurant:
     * - if none => null
     * - if exactly one => item
     * - if >1 => ambiguous (must keep keys unique across restaurant)
     */
    private function findItemByKeyInRestaurant(Restaurant $restaurant, string $key): array
    {
        $q = Item::query()
            ->withTrashed()
            ->where('key', $key)
            ->whereHas('section', fn($s) => $s->where('restaurant_id', $restaurant->id));

        $count = (clone $q)->count();

        if ($count === 0) {
            return ['status' => 'not_found', 'item' => null];
        }
        if ($count > 1) {
            return ['status' => 'ambiguous', 'item' => null];
        }

        return ['status' => 'ok', 'item' => $q->first()];
    }

    private function isUnsafePath(string $path): bool
    {
        return str_contains($path, '..')
            || str_contains($path, '\\')
            || Str::startsWith($path, '/');
    }

    private function err(string $path, string $messageKey, array $params): array
    {
        return [
            'path' => $path,
            'message_key' => $messageKey,
            'params' => $params,
        ];
    }

    private function validateSectionOp(
        Restaurant $restaurant,
        User $user,
        string $type,
        string $action,
        string $key,
        array $op,
        string $base,
        array &$errors,
        array &$plan
    ): void {
        $key = trim($key);

        // найти секцию
        $query = Section::where('restaurant_id', $restaurant->id)
            ->where('key', $key);

        if ($type === 'category') {
            $query->whereNull('parent_id');
        }

        $section = $query->withTrashed()->first();
        $exists = (bool)$section;

        // права
        $permBase = $type === 'category' ? 'categories' : 'subcategories';

        if ($action === 'create') {
            Permissions::abortUnless($user, "{$permBase}.create");

            if ($exists) {
                $errors[] = $this->err("$base.key", 'admin.import.errors.section_exists', ['key'=>$key]);
                return;
            }

            $parentId = null;
            if ($type === 'subcategory') {
                $parentKey = $op['parent']['category_key'] ?? null;
                if (!$parentKey) {
                    $errors[] = $this->err("$base.parent.category_key", 'admin.import.errors.parent_required', []);
                    return;
                }

                $parent = Section::where('restaurant_id',$restaurant->id)
                    ->whereNull('parent_id')
                    ->where('key',$parentKey)
                    ->first();

                if (!$parent) {
                    $errors[] = $this->err("$base.parent", 'admin.import.errors.parent_not_found', ['category_key'=>$parentKey]);
                    return;
                }
                $parentId = $parent->id;
            }

            $set = $this->normalizeSectionSet($op['set'] ?? null, $errors, "$base.set");
            if (!$set) return;

            $plan['ops'][] = [
                'type' => 'section',
                'op' => 'create',
                'key' => $key,
                'parent_id' => $parentId,
                'set' => $set,
            ];
            return;
        }

        // update / delete / toggle
        if (!$exists || $section->trashed()) {
            $errors[] = $this->err("$base.key", 'admin.import.errors.section_not_found', ['key'=>$key]);
            return;
        }

        if ($action === 'delete') {
            Permissions::abortUnless($user, "{$permBase}.delete");

            $plan['ops'][] = [
                'type'=>'section',
                'op'=>'delete',
                'id'=>$section->id
            ];
            return;
        }

        if ($action === 'toggle') {
            Permissions::abortUnless($user, "{$permBase}.toggle");

            if (!isset($op['set']['is_active']) || !is_bool($op['set']['is_active'])) {
                $errors[] = $this->err("$base.set.is_active", 'admin.import.errors.boolean_required', []);
                return;
            }

            $plan['ops'][] = [
                'type'=>'section',
                'op'=>'toggle',
                'id'=>$section->id,
                'is_active'=>$op['set']['is_active'],
            ];
            return;
        }

        if ($action === 'update') {
            Permissions::abortUnless($user, "{$permBase}.edit");

            $set = $this->normalizeSectionSet($op['set'] ?? null, $errors, "$base.set");
            if (!$set) return;

            $plan['ops'][] = [
                'type'=>'section',
                'op'=>'update',
                'id'=>$section->id,
                'set'=>$set,
            ];
        }
    }

    private function normalizeSectionSet(?array $set, array &$errors, string $base): ?array
    {
        if (!is_array($set)) {
            $errors[] = $this->err($base, 'admin.import.errors.set_object_required', []);
            return null;
        }

        $out = [];

        if (array_key_exists('is_active', $set)) {
            if (!is_bool($set['is_active'])) {
                $errors[] = $this->err("$base.is_active", 'admin.import.errors.boolean_required', []);
                return null;
            }
            $out['is_active'] = $set['is_active'];
        }

        if (array_key_exists('translations', $set)) {
            if (!is_array($set['translations'])) {
                $errors[] = $this->err("$base.translations", 'admin.import.errors.translations_object_required', []);
                return null;
            }

            $trs = [];
            foreach ($set['translations'] as $loc=>$tr) {
                if (!in_array($loc,['de','en','ru'],true)) continue;
                if (!is_array($tr)) continue;

                $row=[];
                if (isset($tr['title'])) {
                    $row['title'] = trim(strip_tags($tr['title']));
                }
                if (!empty($row)) $trs[$loc]=$row;
            }

            if (empty($trs)) {
                $errors[] = $this->err("$base.translations", 'admin.import.errors.translation_empty', []);
                return null;
            }

            $out['translations']=$trs;
        }

        if (empty($out)) {
            $errors[] = $this->err($base, 'admin.import.errors.set_empty', []);
            return null;
        }

        return $out;
    }

     private function validateSocialOp(
         Restaurant $restaurant,
         User $user,
         string $action,
         string $key,
         array $op,
         string $base,
         array &$errors,
         array &$plan
     ): void {
         $key = trim($key);

         if (!in_array($action, ['create','update','delete','toggle'], true)) {
             $errors[] = $this->err("$base.op", 'admin.import.errors.social_op_invalid', []);
             return;
         }

         // find by key in restaurant (including trashed for create conflict detection)
         $link = RestaurantSocialLink::query()
             ->withTrashed()
             ->where('restaurant_id', $restaurant->id)
             ->where('key', $key)
             ->first();

         $exists = (bool)$link;
         $isDeleted = $link ? $link->trashed() : false;

         // permissions helper: manage OR granular
         $canEdit   = Permissions::can($user, 'socials_manage') || Permissions::can($user, 'socials.edit');
         $canDelete = Permissions::can($user, 'socials_manage') || Permissions::can($user, 'socials.delete');
         $canToggle = Permissions::can($user, 'socials_manage') || Permissions::can($user, 'socials.toggle.active');
         $canIcon   = Permissions::can($user, 'socials_manage') || Permissions::can($user, 'socials.icon.upload');

         if ($action === 'delete') {
             if (!$exists || $isDeleted) {
                 $errors[] = $this->err("$base.key", 'admin.import.errors.social_not_found', ['key'=>$key]);
                 return;
             }
             if (!$canDelete) {
                 $errors[] = $this->err("$base", 'admin.import.errors.no_permission', ['perm'=>'socials.delete']);
                 return;
             }

             $plan['ops'][] = [
                 'type' => 'social',
                 'op' => 'delete',
                 'id' => $link->id,
                 'key' => $key,
             ];
             $plan['summary']['delete'] = ($plan['summary']['delete'] ?? 0) + 1;
             return;
         }

         // create / update / toggle require set
         $set = $op['set'] ?? null;
         if (!is_array($set)) {
             $errors[] = $this->err("$base.set", 'admin.import.errors.set_object_required', []);
             return;
         }

         if ($action === 'toggle') {
             if (!$exists || $isDeleted) {
                 $errors[] = $this->err("$base.key", 'admin.import.errors.social_not_found', ['key'=>$key]);
                 return;
             }
             if (!$canToggle) {
                 $errors[] = $this->err("$base", 'admin.import.errors.no_permission', ['perm'=>'socials.toggle.active']);
                 return;
             }
             if (!isset($set['is_active']) || !is_bool($set['is_active'])) {
                 $errors[] = $this->err("$base.set.is_active", 'admin.import.errors.boolean_required', []);
                 return;
             }

             $plan['ops'][] = [
                 'type' => 'social',
                 'op' => 'toggle',
                 'id' => $link->id,
                 'key' => $key,
                 'is_active' => $set['is_active'],
             ];
             $plan['summary']['update'] = ($plan['summary']['update'] ?? 0) + 1;
             return;
         }

         if ($action === 'create') {
             if ($exists && !$isDeleted) {
                 $errors[] = $this->err("$base.key", 'admin.import.errors.social_exists', ['key'=>$key]);
                 return;
             }
             if (!$canEdit) {
                 $errors[] = $this->err("$base", 'admin.import.errors.no_permission', ['perm'=>'socials.edit']);
                 return;
             }

             // лимит create
             $max = $this->socialMaxAllowed($user);
             $currentCount = RestaurantSocialLink::query()
                 ->where('restaurant_id', $restaurant->id)
                 ->whereNull('deleted_at')
                 ->count();

             if (($currentCount + 1) > $max) {
                 $errors[] = $this->err("$base", 'admin.import.errors.social_limit_exceeded', [
                     'max' => $max,
                     'count' => $currentCount + 1,
                 ]);
                 return;
             }

             $normalized = $this->normalizeSocialSet($set, $errors, $base, $canIcon, $user);

             if (empty($normalized)) {
                 $errors[] = $this->err("$base.set", 'admin.import.errors.set_empty', []);
                 return;
             }

             $plan['ops'][] = [
                 'type' => 'social',
                 'op' => 'create',
                 'key' => $key,
                 'set' => $normalized,
                 'restore_id' => ($exists && $isDeleted) ? $link->id : null, // optional: restore soft-deleted link by same key
             ];
             $plan['summary']['create'] = ($plan['summary']['create'] ?? 0) + 1;
             return;
         }

         // update
         if ($action === 'update') {
             if (!$exists || $isDeleted) {
                 $errors[] = $this->err("$base.key", 'admin.import.errors.social_not_found', ['key'=>$key]);
                 return;
             }
             if (!$canEdit) {
                 $errors[] = $this->err("$base", 'admin.import.errors.no_permission', ['perm'=>'socials.edit']);
                 return;
             }

             $normalized = $this->normalizeSocialSet($set, $errors, $base, $canIcon, $user);

             if (empty($normalized)) {
                 $errors[] = $this->err("$base.set", 'admin.import.errors.set_empty', []);
                 return;
             }

             $plan['ops'][] = [
                 'type' => 'social',
                 'op' => 'update',
                 'id' => $link->id,
                 'key' => $key,
                 'set' => $normalized,
             ];
             $plan['summary']['update'] = ($plan['summary']['update'] ?? 0) + 1;
         }
     }

     private function normalizeSocialSet(array $set, array &$errors, string $base, bool $canIcon, User $user): array
     {
         $out = [];

         $cleanStr = function ($v): ?string {
             if (!is_string($v)) return null;
             $v = trim(strip_tags($v));
             $v = preg_replace('/\s+/u', ' ', $v);
             return $v === '' ? null : $v;
         };

         if (array_key_exists('title', $set)) {
             $t = $cleanStr($set['title']);
             if ($t === null) $errors[] = $this->err("$base.set.title", 'admin.import.errors.social_title_invalid', []);
             else $out['title'] = $t;
         }

         if (array_key_exists('url', $set)) {
             $u = $cleanStr($set['url']);
             if ($u === null || !preg_match('#^https://#i', $u)) {
                 $errors[] = $this->err("$base.set.url", 'admin.import.errors.social_url_invalid', []);
             } else {
                 $out['url'] = $u;
             }
         }

         if (array_key_exists('is_active', $set)) {
             if (!is_bool($set['is_active'])) $errors[] = $this->err("$base.set.is_active", 'admin.import.errors.boolean_required', []);
             else $out['is_active'] = $set['is_active'];
         }

         if (array_key_exists('icon', $set)) {
             if (!$canIcon) {
                 $errors[] = $this->err("$base.set.icon", 'admin.import.errors.no_permission', ['perm'=>'socials.icon.upload']);
             } else {
                 $icon = $set['icon'];
                 if ($icon === null) {
                     $out['icon'] = null;
                 } else {
                     $icon = $cleanStr($icon);
                     if ($icon === null) {
                         $errors[] = $this->err("$base.set.icon", 'admin.import.errors.social_icon_invalid', []);
                     } else {
                         if ($this->isUnsafePath($icon)) {
                             $errors[] = $this->err("$base.set.icon", 'admin.import.errors.path_unsafe', []);
                         } else {
                             // если иконка приходит как путь (из ZIP ассетов) — требуем ZIP право
                             Permissions::abortUnless($user, 'import.images_zip');
                             $out['icon'] = $icon;
                         }
                     }
                 }
             }
         }

         return $out;
     }

     private function socialMaxAllowed(User $user): int
     {
         if (Permissions::can($user, 'socials.add.5')) return 5;
         if (Permissions::can($user, 'socials.add.4')) return 4;
         if (Permissions::can($user, 'socials.add.3')) return 3;
         return 2;
     }

     private function validateReorderOp(
         Restaurant $restaurant,
         User $user,
         string $type,
         array $op,
         string $base,
         array &$errors,
         array &$plan
     ): void {
         if (!isset($op['keys']) || !is_array($op['keys']) || empty($op['keys'])) {
             $errors[] = $this->err("$base.keys", 'admin.import.errors.keys_required', []);
             return;
         }

         // permissions
         $permMap = [
             'category'    => 'categories.edit',
             'subcategory' => 'subcategories.edit',
             'item'        => 'items.edit',
             'social'      => 'socials_manage',
         ];

         if (!isset($permMap[$type])) {
             $errors[] = $this->err("$base.type", 'admin.import.errors.type_not_supported', []);
             return;
         }

         Permissions::abortUnless($user, $permMap[$type]);

         $keys = array_values(array_unique(array_map('trim', $op['keys'])));
         if (count($keys) !== count($op['keys'])) {
             $errors[] = $this->err("$base.keys", 'admin.import.errors.keys_duplicate', []);
             return;
         }

         // fetch real keys from DB
         $dbKeys = match ($type) {
             'category' => Section::where('restaurant_id', $restaurant->id)
                 ->whereNull('parent_id')
                 ->pluck('key')
                 ->toArray(),

             'subcategory' => Section::where('restaurant_id', $restaurant->id)
                 ->whereNotNull('parent_id')
                 ->pluck('key')
                 ->toArray(),

             'item' => Item::whereHas('section', fn($s) =>
                     $s->where('restaurant_id', $restaurant->id)
                 )->pluck('key')->toArray(),

             'social' => \App\Models\RestaurantSocialLink::where('restaurant_id', $restaurant->id)
                 ->whereNull('deleted_at')
                 ->pluck('key')
                 ->toArray(),
         };

         sort($dbKeys);
         $sortedKeys = $keys;
         sort($sortedKeys);

         if ($sortedKeys !== $dbKeys) {
             $errors[] = $this->err("$base.keys", 'admin.import.errors.keys_mismatch', []);
             return;
         }

         $plan['ops'][] = [
             'type' => $type,
             'op' => 'reorder',
             'keys' => $keys,
         ];
     }





}
