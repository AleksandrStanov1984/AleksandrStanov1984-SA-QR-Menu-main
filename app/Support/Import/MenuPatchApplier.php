<?php

namespace App\Support\Import;

use App\Models\Item;
use App\Models\ItemTranslation;
use App\Models\Restaurant;
use App\Models\RestaurantSocialLink;
use App\Models\Section;
use App\Models\SectionTranslation;
use Illuminate\Support\Facades\DB;

class MenuPatchApplier
{
    public function apply(Restaurant $restaurant, array $plan): array
    {
        $result = [
            'created' => 0,
            'updated' => 0,
            'deleted' => 0,
        ];

        DB::transaction(function () use ($restaurant, $plan, &$result) {

            // =========================
            // CREATE MODE
            // =========================
            // IMPORTANT:
            // full wipe must happen ONCE
            // before all category imports
            if (($plan['mode'] ?? null) === 'create') {

                $this->wipeRestaurantMenu(
                    $restaurant,
                    $result
                );
            }

            foreach ($plan['ops'] as $op) {

                // =========================
                // CATEGORY IMPORT
                // =========================
                if (
                    $op['type'] === 'category' &&
                    in_array($op['op'], [
                        'create',
                        'add',
                        'update',

                        // TEMP backward compatibility
                        'replace',
                    ], true)
                ) {

                    $mode = $op['op'];

                    // TEMP:
                    // old replace behaves as create
                    if ($mode === 'replace') {
                        $mode = 'create';
                    }

                    $this->applyCategoryMode(
                        $restaurant,
                        $mode,
                        $op['data'],
                        $result
                    );

                    continue;
                }

                // =========================
                // LEGACY OPS
                // =========================
                // TODO:
                // remove after migration
                if ($op['type'] === 'item') {
                    $this->applyItemOp($restaurant, $op, $result);
                }

                if ($op['type'] === 'section') {
                    $this->applySectionOp($restaurant, $op, $result);
                }

                if ($op['type'] === 'social') {
                    $this->applySocialOp($restaurant, $op, $result);
                }

                if (($op['op'] ?? null) === 'reorder') {
                    $this->applyReorder($restaurant, $op);
                }
            }
        });

        return $result;
    }

    private function applyCategoryMode(
        Restaurant $restaurant,
        string $mode,
        array $cat,
        array &$result
    ): void {

        // TODO:
        // future translation pipeline hook
        //
        // Example:
        // TranslationSyncJob::dispatch(...)
        //
        // Current MVP:
        // translations imported directly

        match ($mode) {

            'create' => $this->applyCreateCategory(
                $restaurant,
                $cat,
                $result
            ),

            'add' => $this->applyAddCategory(
                $restaurant,
                $cat,
                $result
            ),

            'update' => $this->applyUpdateCategory(
                $restaurant,
                $cat,
                $result
            ),

            default => null,
        };
    }

    private function wipeRestaurantMenu(
        Restaurant $restaurant,
        array &$result
    ): void {

        // TODO:
        // future dedicated deletion service
        //
        // Must safely remove:
        // - categories
        // - subcategories
        // - items
        // - translations
        // - public generated assets
        // - generated cache
        //
        // Storage inbox should remain untouched

        // =========================
        // DELETE ITEMS SAFE
        // =========================
        Item::query()
            ->whereHas('section', function ($q) use ($restaurant) {
                $q->where('restaurant_id', $restaurant->id);
            })
            ->get()
            ->each(function (Item $item) {

                if ($item->image_path) {

                    app(\App\Services\ImageService::class)
                        ->delete($item->image_path);
                }

                $item->delete();
            });

        // =========================
        // DELETE SECTIONS SAFE
        // =========================
        Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->get()
            ->each(function (Section $section) {

                $section->delete();
            });

        $result['deleted']++;
    }

    private function applyCreateCategory(
        Restaurant $restaurant,
        array $cat,
        array &$result
    ): void {

        // =========================
        // CREATE MODE
        // =========================
        // Rules:
        // - menu already wiped globally
        // - create everything fresh
        // - images later re-imported
        // - no overwrite logic needed

        $this->createOrSyncCategoryTree(
            $restaurant,
            $cat,
            $result,
            false,
            false
        );
    }

    private function applyAddCategory(
        Restaurant $restaurant,
        array $cat,
        array &$result
    ): void {

        // =========================
        // ADD MODE
        // =========================
        // Rules:
        // - never delete existing
        // - never overwrite existing
        // - only add missing:
        //   categories
        //   subcategories
        //   items
        //
        // Existing images untouched

        $this->createOrSyncCategoryTree(
            $restaurant,
            $cat,
            $result,
            true,
            false
        );
    }

    private function applyUpdateCategory(
        Restaurant $restaurant,
        array $cat,
        array &$result
    ): void {

        // =========================
        // UPDATE MODE
        // =========================
        // Rules:
        // - update only changed fields
        // - compare DB vs JSON
        // - avoid unnecessary save()
        // - keep existing images
        // - keep existing structure
        // - update by keys only
        //
        // Future:
        // deep diff update service

        $this->createOrSyncCategoryTree(
            $restaurant,
            $cat,
            $result,
            true,
            true
        );
    }

    private function createOrSyncCategoryTree(
        Restaurant $restaurant,
        array $cat,
        array &$result,
        bool $skipExisting,
        bool $updateExisting
    ): void {

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

        if (!$skipExisting || $section->wasRecentlyCreated || $updateExisting) {

            $this->syncSectionTranslations(
                $section,
                $cat['translations'] ?? [],
                $updateExisting
            );
        }

        if ($section->wasRecentlyCreated) {
            $result['created']++;
        }

        // =========================
        // ITEMS
        // =========================
        foreach ($cat['items'] ?? [] as $itemData) {

            $this->createOrSyncItem(
                $section,
                $itemData,
                $result,
                $skipExisting,
                $updateExisting
            );
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

            if (
                !$skipExisting ||
                $subSection->wasRecentlyCreated ||
                $updateExisting
            ) {

                $this->syncSectionTranslations(
                    $subSection,
                    $sub['translations'] ?? [],
                    $updateExisting
                );
            }

            if ($subSection->wasRecentlyCreated) {
                $result['created']++;
            }

            foreach ($sub['items'] ?? [] as $itemData) {

                $this->createOrSyncItem(
                    $subSection,
                    $itemData,
                    $result,
                    $skipExisting,
                    $updateExisting
                );
            }
        }
    }

    private function createOrSyncItem(
        Section $section,
        array $itemData,
        array &$result,
        bool $skipExisting,
        bool $updateExisting
    ): void {

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

        // =========================
        // ADD MODE
        // =========================
        // existing item => skip
        if (
            $skipExisting &&
            !$updateExisting &&
            !$item->wasRecentlyCreated
        ) {
            return;
        }

        $this->applyItemSet(
            $item,
            $itemData['set'] ?? [],
            $updateExisting
        );

        if ($item->wasRecentlyCreated) {
            $result['created']++;
        } else {
            $result['updated']++;
        }
    }

    private function syncSectionTranslations(
        Section $section,
        array $translations,
        bool $onlyChanged
    ): void {

        foreach ($translations as $loc => $tr) {

            $t = SectionTranslation::firstOrNew([
                'section_id' => $section->id,
                'locale' => $loc,
            ]);

            $changed = false;

            foreach (['title', 'description'] as $field) {

                if (!isset($tr[$field])) {
                    continue;
                }

                if (!$onlyChanged || $t->$field !== $tr[$field]) {

                    $t->$field = $tr[$field];

                    $changed = true;
                }
            }

            if ($changed || !$t->exists) {
                $t->save();
            }
        }
    }

    private function applyItemOp(
        Restaurant $restaurant,
        array $op,
        array &$result
    ): void {

        if ($op['op'] === 'delete') {

            $item = Item::where('id', $op['item_id'])
                ->firstOrFail();

            $item->delete();

            $result['deleted']++;

            return;
        }

        if ($op['op'] === 'update') {

            $item = Item::where('id', $op['item_id'])
                ->firstOrFail();

            $this->applyItemSet(
                $item,
                $op['set'] ?? [],
                true
            );

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

            $this->applyItemSet(
                $item,
                $op['set'] ?? [],
                false
            );

            $result['created']++;
        }
    }

    private function resolveSectionByKeys(
        Restaurant $restaurant,
        string $catKey,
        ?string $subKey
    ): Section {

        $cat = Section::where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->where('key', $catKey)
            ->firstOrFail();

        if (!$subKey) {
            return $cat;
        }

        return Section::where('restaurant_id', $restaurant->id)
            ->where('parent_id', $cat->id)
            ->where('key', $subKey)
            ->firstOrFail();
    }

    private function applyItemSet(
        Item $item,
        array $set,
        bool $onlyChanged = false
    ): void {

        $changed = false;

        foreach (['price', 'currency', 'is_active'] as $f) {

            if (!array_key_exists($f, $set)) {
                continue;
            }

            if (!$onlyChanged || $item->$f != $set[$f]) {

                $item->$f = $set[$f];

                $changed = true;
            }
        }

        if (array_key_exists('image', $set)) {

            if (
                !$onlyChanged ||
                $item->image_path !== $set['image']
            ) {

                $item->image_path = $set['image'];

                $changed = true;
            }
        }

        if (isset($set['meta'])) {

            $mergedMeta = array_merge(
                $item->meta ?? [],
                $set['meta']
            );

            if (
                !$onlyChanged ||
                ($item->meta ?? []) !== $mergedMeta
            ) {

                $item->meta = $mergedMeta;

                $changed = true;
            }
        }

        if ($changed || !$item->exists) {
            $item->save();
        }

        if (isset($set['translations'])) {

            foreach ($set['translations'] as $loc => $tr) {

                $t = ItemTranslation::firstOrNew([
                    'item_id' => $item->id,
                    'locale' => $loc,
                ]);

                $translationChanged = false;

                foreach ([
                             'title',
                             'description',
                             'details',
                         ] as $f) {

                    if (!isset($tr[$f])) {
                        continue;
                    }

                    if (
                        !$onlyChanged ||
                        $t->$f !== $tr[$f]
                    ) {

                        $t->$f = $tr[$f];

                        $translationChanged = true;
                    }
                }

                if ($translationChanged || !$t->exists) {
                    $t->save();
                }
            }
        }
    }

    private function applySectionOp(
        Restaurant $restaurant,
        array $op,
        array &$result
    ): void {

        if ($op['op'] === 'create') {

            $s = new Section();

            $s->restaurant_id = $restaurant->id;
            $s->parent_id = $op['parent_id'] ?? null;
            $s->key = $op['key'];
            $s->is_active = true;

            $s->save();

            $this->applySectionSet(
                $s,
                $op['set'] ?? []
            );

            $result['created']++;
        }

        if ($op['op'] === 'delete') {

            Section::where('id', $op['id'])
                ->firstOrFail()
                ->delete();

            $result['deleted']++;
        }

        if ($op['op'] === 'update') {

            $s = Section::where('id', $op['id'])
                ->firstOrFail();

            $this->applySectionSet(
                $s,
                $op['set'] ?? []
            );

            $result['updated']++;
        }
    }

    private function applySectionSet(Section $s, array $set): void
    {
        if (isset($set['is_active'])) {
            $s->is_active = $set['is_active'];
        }

        $s->save();

        if (isset($set['translations'])) {

            foreach ($set['translations'] as $loc => $tr) {

                $t = SectionTranslation::firstOrNew([
                    'section_id' => $s->id,
                    'locale' => $loc,
                ]);

                if (isset($tr['title'])) {
                    $t->title = $tr['title'];
                }

                if (isset($tr['description'])) {
                    $t->description = $tr['description'];
                }

                $t->save();
            }
        }
    }

    private function applySocialOp(
        Restaurant $restaurant,
        array $op,
        array &$result
    ): void {

        if ($op['op'] === 'create') {

            $l = new RestaurantSocialLink();

            $l->restaurant_id = $restaurant->id;
            $l->key = $op['key'];
            $l->is_active = true;

            $l->save();

            $result['created']++;
        }

        if ($op['op'] === 'delete') {

            RestaurantSocialLink::where('id', $op['id'])
                ->firstOrFail()
                ->delete();

            $result['deleted']++;
        }
    }

    private function applyReorder(
        Restaurant $restaurant,
        array $op
    ): void {}
}
