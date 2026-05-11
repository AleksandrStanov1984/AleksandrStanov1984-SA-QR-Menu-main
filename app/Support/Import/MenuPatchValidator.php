<?php

namespace App\Support\Import;

use App\Models\Restaurant;
use App\Models\User;

class MenuPatchValidator
{
    public function validate(
        array $payload,
        User $user,
        Restaurant $restaurant,
        ?string $forcedMode = null
    ): array {

        $errors = [];

        $plan = [
            'mode' => null,

            // TODO:
            // old ops architecture still temporary kept
            // until create/add/update services are fully migrated
            'ops' => [],

            'summary' => [
                'create' => 0,
                'update' => 0,
                'delete' => 0,
            ],

            // future-ready normalized structure
            'categories' => [],
        ];

        // =========================
        // MODE
        // =========================
        $mode = $forcedMode
            ?? $payload['mode']
            ?? null;

        if (!in_array($mode, [
            'create',
            'add',
            'update',

            // TODO:
            // delete mode reserved for future implementation
            // 'delete',

            // TEMP:
            // keep replace for backward compatibility
            'replace',
        ], true)) {

            $errors[] = $this->err(
                'mode',
                'admin.import.errors.mode_invalid',
                []
            );

            return compact('errors', 'plan');
        }

        // =========================
        // BACKWARD COMPATIBILITY
        // =========================
        // TEMP:
        // old replace mode behaves as create
        if ($mode === 'replace') {
            $mode = 'create';
        }

        $plan['mode'] = $mode;

        // =========================
        // CATEGORIES REQUIRED
        // =========================
        if (
            !isset($payload['categories']) ||
            !is_array($payload['categories'])
        ) {
            $errors[] = $this->err(
                'categories',
                'admin.import.errors.categories_required',
                []
            );

            return compact('errors', 'plan');
        }

        // =========================
        // NORMALIZE CATEGORIES
        // =========================
        foreach ($payload['categories'] as $ci => $cat) {

            $base = "categories.$ci";

            $category = $this->normalizeCategory(
                $cat,
                $errors,
                $base
            );

            if (!$category) {
                continue;
            }

            // =========================
            // FUTURE STRUCTURE
            // =========================
            $plan['categories'][] = $category;

            // =========================
            // TEMP OPS SUPPORT
            // =========================
            // keep old applier working
            $plan['ops'][] = [
                'type' => 'category',
                'op' => $mode,
                'data' => $category,
            ];

            // =========================
            // SUMMARY
            // =========================
            match ($mode) {

                'create',
                'add' => $plan['summary']['create']++,

                'update' => $plan['summary']['update']++,

                // future-ready
                'delete' => $plan['summary']['delete']++,

                default => null,
            };
        }

        // TODO:
        // later:
        // translation pipeline integration
        //
        // Example:
        // TranslationSyncJob::dispatch(...)
        //
        // Current MVP:
        // translations are imported directly from JSON

        return compact('errors', 'plan');
    }

    // =========================
    // CATEGORY
    // =========================
    private function normalizeCategory(array $cat, array &$errors, string $base): ?array
    {
        $key = $cat['key'] ?? null;

        if (!$key || !is_string($key)) {

            $errors[] = $this->err(
                "$base.key",
                'admin.import.errors.key_required',
                []
            );

            return null;
        }

        $out = [
            'key' => trim($key),

            'type' => $cat['type'] ?? 'food',

            'is_active' => (bool)($cat['is_active'] ?? true),

            'translations' => $this->normalizeTranslations(
                $cat['translations'] ?? [],
                $errors,
                "$base.translations"
            ),

            'items' => [],

            'subcategories' => [],
        ];

        // =========================
        // ITEMS
        // =========================
        foreach ($cat['items'] ?? [] as $ii => $item) {

            $normalized = $this->normalizeItem(
                $item,
                $errors,
                "$base.items.$ii"
            );

            if ($normalized) {
                $out['items'][] = $normalized;
            }
        }

        // =========================
        // SUBCATEGORIES
        // =========================
        foreach ($cat['subcategories'] ?? [] as $si => $sub) {

            $subNormalized = $this->normalizeCategory(
                $sub,
                $errors,
                "$base.subcategories.$si"
            );

            if ($subNormalized) {
                $out['subcategories'][] = $subNormalized;
            }
        }

        return $out;
    }

    // =========================
    // ITEM
    // =========================
    private function normalizeItem(array $item, array &$errors, string $base): ?array
    {
        $key = $item['key'] ?? null;

        if (!$key || !is_string($key)) {

            $errors[] = $this->err(
                "$base.key",
                'admin.import.errors.key_required',
                []
            );

            return null;
        }

        $set = [
            'price' => $item['price'] ?? null,

            'currency' => $item['currency'] ?? 'EUR',

            'is_active' => (bool)($item['is_active'] ?? true),

            'meta' => $item['meta'] ?? [],

            'translations' => $item['translations'] ?? [],
        ];

        $normalizedSet = $this->normalizeItemSet(
            $set,
            $errors,
            "$base"
        );

        return [
            'key' => trim($key),

            'set' => $normalizedSet,
        ];
    }

    // =========================
    // ITEM SET
    // =========================
    private function normalizeItemSet(array $set, array &$errors, string $base): array
    {
        $out = [];

        // =========================
        // PRICE
        // =========================
        if (isset($set['price'])) {

            $price = trim((string)$set['price']);

            if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {

                $errors[] = $this->err(
                    "$base.price",
                    'admin.import.errors.price_invalid',
                    []
                );

            } else {

                $out['price'] = $price;
            }
        }

        // =========================
        // CURRENCY
        // =========================
        if (isset($set['currency'])) {
            $out['currency'] = strtoupper(
                trim((string)$set['currency'])
            );
        }

        // =========================
        // ACTIVE
        // =========================
        if (isset($set['is_active'])) {
            $out['is_active'] = (bool)$set['is_active'];
        }

        // =========================
        // META
        // =========================
        if (isset($set['meta']) && is_array($set['meta'])) {
            $out['meta'] = $set['meta'];
        }

        // =========================
        // TRANSLATIONS
        // =========================
        if (isset($set['translations'])) {

            $out['translations'] = $this->normalizeTranslations(
                $set['translations'],
                $errors,
                "$base.translations"
            );
        }

        return $out;
    }

    // =========================
    // TRANSLATIONS
    // =========================
    private function normalizeTranslations(
        array $trs,
        array &$errors,
        string $base
    ): array {

        $out = [];

        foreach ($trs as $locale => $data) {

            if (!in_array($locale, ['de', 'en', 'ru'], true)) {
                continue;
            }

            if (!is_array($data)) {
                continue;
            }

            $row = [];

            foreach ([
                         'title',
                         'description',
                         'details',
                     ] as $field) {

                if (!isset($data[$field])) {
                    continue;
                }

                $row[$field] = trim(
                    strip_tags((string)$data[$field])
                );
            }

            if (!empty($row)) {
                $out[$locale] = $row;
            }
        }

        return $out;
    }

    // =========================
    // ERROR
    // =========================
    private function err(
        string $path,
        string $key,
        array $params
    ): array {

        return [
            'path' => $path,
            'message_key' => $key,
            'params' => $params,
        ];
    }
}
