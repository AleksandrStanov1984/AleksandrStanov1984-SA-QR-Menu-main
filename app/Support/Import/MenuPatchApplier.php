<?php
namespace App\Support\Import;

use App\Models\Item;
use App\Models\ItemTranslation;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use App\Models\RestaurantSocialLink;
use Illuminate\Support\Facades\DB;

class MenuPatchApplier
{
    public function apply(Restaurant $restaurant, array $plan): array
    {
        $result = ['created'=>0,'updated'=>0,'deleted'=>0];

        DB::transaction(function () use ($restaurant, $plan, &$result) {
            foreach ($plan['ops'] as $op) {
                if ($op['op'] === 'replace' && $op['type'] === 'category') {
                    $this->applyReplaceCategory($restaurant, $op['data'], $result);
                    continue;
                }

                if ($op['type'] === 'item') {
                    $this->applyItemOp($restaurant, $op, $result);
                }
                if ($op['type'] === 'section') {
                    $this->applySectionOp($restaurant, $op, $result);
                }
                if ($op['type'] === 'social') {
                    $this->applySocialOp($restaurant, $op, $result);
                }
                if ($op['op'] === 'reorder') {
                    $this->applyReorder($restaurant, $op);
                }
            }
        });

        return $result;
    }

    private function applyItemOp(Restaurant $restaurant, array $op, array &$result): void
    {
        if ($op['op'] === 'delete') {
            $item = Item::where('id',$op['item_id'])->firstOrFail();
            $item->delete();
            $result['deleted']++;
            return;
        }

        if ($op['op'] === 'update') {
            $item = Item::where('id',$op['item_id'])->firstOrFail();
            $this->applyItemSet($item,$op['set']??[]);
            $result['updated']++;
            return;
        }

        if ($op['op'] === 'create') {
            $parent = $op['parent'] ?? null;
            $section = $this->resolveSectionByKeys(
                $restaurant,
                $parent['category_key'] ?? null,
                $parent['subcategory_key'] ?? null
            );

            $item = new Item();
            $item->section_id = $section->id;
            $item->key = $op['key'];
            $item->currency = 'EUR';
            $item->is_active = true;
            $item->save();

            $this->applyItemSet($item,$op['set']??[]);
            $result['created']++;
        }
    }

    private function resolveSectionByKeys(Restaurant $restaurant, string $catKey, ?string $subKey): Section
    {
        $cat = Section::where('restaurant_id',$restaurant->id)
            ->whereNull('parent_id')->where('key',$catKey)->firstOrFail();

        if (!$subKey) return $cat;

        return Section::where('restaurant_id',$restaurant->id)
            ->where('parent_id',$cat->id)->where('key',$subKey)->firstOrFail();
    }

    private function applyItemSet(Item $item, array $set): void
    {
        foreach (['price','currency','is_active'] as $f) {
            if (array_key_exists($f,$set)) $item->$f = $set[$f];
        }
        if (array_key_exists('image',$set)) $item->image_path = $set['image'];
        if (isset($set['meta'])) $item->meta = array_merge($item->meta??[],$set['meta']);
        $item->save();

        if (isset($set['translations'])) {
            foreach ($set['translations'] as $loc=>$tr) {
                $t = ItemTranslation::firstOrNew(['item_id'=>$item->id,'locale'=>$loc]);
                foreach (['title','description','details'] as $f) {
                    if (isset($tr[$f])) $t->$f = $tr[$f];
                }
                $t->save();
            }
        }
    }

    private function applySectionOp(Restaurant $restaurant, array $op, array &$result): void
    {
        if ($op['op']==='create') {
            $s = new Section();
            $s->restaurant_id = $restaurant->id;
            $s->parent_id = $op['parent_id'] ?? null;
            $s->key = $op['key'];
            $s->is_active = true;
            $s->save();
            $this->applySectionSet($s,$op['set']??[]);
            $result['created']++;
        }
        if ($op['op']==='delete') {
            Section::where('id',$op['id'])->firstOrFail()->delete();
            $result['deleted']++;
        }
        if ($op['op']==='update') {
            $s = Section::where('id',$op['id'])->firstOrFail();
            $this->applySectionSet($s,$op['set']??[]);
            $result['updated']++;
        }
    }

    private function applySectionSet(Section $s, array $set): void
    {
        if (isset($set['is_active'])) $s->is_active=$set['is_active'];
        $s->save();
        if (isset($set['translations'])) {
            foreach ($set['translations'] as $loc=>$tr) {
                $t = SectionTranslation::firstOrNew(['section_id'=>$s->id,'locale'=>$loc]);
                if (isset($tr['title'])) $t->title=$tr['title'];
                $t->save();
            }
        }
    }

    private function applySocialOp(Restaurant $restaurant, array $op, array &$result): void
    {
        if ($op['op']==='create') {
            $l = new RestaurantSocialLink();
            $l->restaurant_id=$restaurant->id;
            $l->key=$op['key'];
            $l->is_active=true;
            $l->save();
            $result['created']++;
        }
        if ($op['op']==='delete') {
            RestaurantSocialLink::where('id',$op['id'])->firstOrFail()->delete();
            $result['deleted']++;
        }
    }

    private function applyReorder(Restaurant $restaurant, array $op): void {}

    private function applyReplaceCategory(Restaurant $restaurant, array $cat, array &$result): void
    {
        // =========================
        // CATEGORY
        // =========================
        $section = Section::firstOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'key' => $cat['key'],
                'parent_id' => null,
            ],
            [
                'is_active' => $cat['is_active'] ?? true,
                'type' => $cat['type'] ?? 'food',
            ]
        );

        foreach ($cat['translations'] ?? [] as $loc => $tr) {
            $t = SectionTranslation::firstOrNew([
                'section_id' => $section->id,
                'locale' => $loc
            ]);
            $t->title = $tr['title'] ?? '';
            $t->description = $tr['description'] ?? '';
            $t->save();
        }

        $result['created']++;

        // =========================
        // ITEMS
        // =========================
        foreach ($cat['items'] ?? [] as $itemData) {
            $this->createItemFromReplace($section, $itemData, $result);
        }

        // =========================
        // SUBCATEGORIES
        // =========================
        foreach ($cat['subcategories'] ?? [] as $sub) {

            $subSection = Section::firstOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'key' => $sub['key'],
                    'parent_id' => $section->id,
                ],
                [
                    'is_active' => $sub['is_active'] ?? true,
                    'type' => $sub['type'] ?? 'food',
                ]
            );

            foreach ($sub['translations'] ?? [] as $loc => $tr) {
                $t = SectionTranslation::firstOrNew([
                    'section_id' => $subSection->id,
                    'locale' => $loc
                ]);
                $t->title = $tr['title'] ?? '';
                $t->description = $tr['description'] ?? '';
                $t->save();
            }

            $result['created']++;

            foreach ($sub['items'] ?? [] as $itemData) {
                $this->createItemFromReplace($subSection, $itemData, $result);
            }
        }
    }

    private function createItemFromReplace(Section $section, array $itemData, array &$result): void
    {
        $item = Item::firstOrCreate(
            [
                'section_id' => $section->id,
                'key' => $itemData['key'],
            ],
            [
                'currency' => $itemData['set']['currency'] ?? 'EUR',
                'is_active' => $itemData['set']['is_active'] ?? true,
            ]
        );

        $this->applyItemSet($item, $itemData['set'] ?? []);

        $result['created']++;
    }
}
