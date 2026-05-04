<?php

namespace App\Support\Import;

use App\Models\Restaurant;
use App\Models\User;

class MenuPatchValidator
{
    public function validate(array $payload, User $user, Restaurant $restaurant): array
    {
        $errors = [];

        $plan = [
            'mode' => null,
            'ops' => [],
            'summary' => [
                'create' => 0,
                'update' => 0,
                'delete' => 0,
            ],
        ];

        // =========================
        // MODE
        // =========================
        $mode = $payload['mode'] ?? null;

        if (!in_array($mode, ['patch', 'replace'], true)) {
            $errors[] = $this->err('mode', 'admin.import.errors.mode_invalid', []);
            return compact('errors', 'plan');
        }

        $plan['mode'] = $mode;

        // =========================
        // PATCH MODE
        // =========================
        if ($mode === 'patch') {

            if (!isset($payload['operations']) || !is_array($payload['operations'])) {
                $errors[] = $this->err('operations', 'admin.import.errors.operations_required', []);
                return compact('errors', 'plan');
            }

            foreach ($payload['operations'] as $i => $op) {

                $base = "operations.$i";

                if (!is_array($op)) {
                    $errors[] = $this->err($base, 'admin.import.errors.operation_invalid', []);
                    continue;
                }

                $type = $op['type'] ?? null;
                $action = $op['op'] ?? null;
                $key = $op['key'] ?? null;

                if (!$type || !$action || !$key) {
                    $errors[] = $this->err($base, 'admin.import.errors.operation_invalid', []);
                    continue;
                }

                if ($type === 'item' && in_array($action, ['upsert', 'create', 'update'], true)) {

                    $set = $this->normalizeItemSet($op['set'] ?? [], $errors, "$base.set");

                    if (!empty($set)) {
                        $plan['ops'][] = [
                            'type' => 'item',
                            'op' => $action === 'upsert' ? 'upsert' : $action,
                            'key' => $key,
                            'parent' => $op['parent'] ?? null,
                            'set' => $set,
                        ];
                        $plan['summary']['update']++;
                    }
                }
            }

            return compact('errors', 'plan');
        }

        // =========================
        // REPLACE MODE
        // =========================
        if ($mode === 'replace') {

            if (!isset($payload['categories']) || !is_array($payload['categories'])) {
                $errors[] = $this->err('categories', 'admin.import.errors.categories_required', []);
                return compact('errors', 'plan');
            }

            foreach ($payload['categories'] as $ci => $cat) {

                $base = "categories.$ci";

                $category = $this->normalizeCategory($cat, $errors, $base);

                if (!$category) {
                    continue;
                }

                $plan['ops'][] = [
                    'type' => 'category',
                    'op' => 'replace',
                    'data' => $category,
                ];
            }

            return compact('errors', 'plan');
        }

        return compact('errors', 'plan');
    }

    // =========================
    // CATEGORY
    // =========================
    private function normalizeCategory(array $cat, array &$errors, string $base): ?array
    {
        $key = $cat['key'] ?? null;

        if (!$key || !is_string($key)) {
            $errors[] = $this->err("$base.key", 'admin.import.errors.key_required', []);
            return null;
        }

        $out = [
            'key' => $key,
            'type' => $cat['type'] ?? 'food',
            'is_active' => (bool)($cat['is_active'] ?? true),
            'translations' => $this->normalizeTranslations($cat['translations'] ?? [], $errors, "$base.translations"),
            'items' => [],
            'subcategories' => [],
        ];

        // ITEMS
        foreach ($cat['items'] ?? [] as $ii => $item) {
            $normalized = $this->normalizeItem($item, $errors, "$base.items.$ii");
            if ($normalized) {
                $out['items'][] = $normalized;
            }
        }

        // SUBCATEGORIES
        foreach ($cat['subcategories'] ?? [] as $si => $sub) {
            $subNormalized = $this->normalizeCategory($sub, $errors, "$base.subcategories.$si");
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

        if (!$key) {
            $errors[] = $this->err("$base.key", 'admin.import.errors.key_required', []);
            return null;
        }

        $set = [
            'price' => $item['price'] ?? null,
            'currency' => $item['currency'] ?? 'EUR',
            'is_active' => (bool)($item['is_active'] ?? true),
            'meta' => $item['meta'] ?? [],
            'translations' => $item['translations'] ?? [],
        ];

        $normalizedSet = $this->normalizeItemSet($set, $errors, "$base");

        return [
            'key' => $key,
            'set' => $normalizedSet,
        ];
    }

    // =========================
    // ITEM SET
    // =========================
    private function normalizeItemSet(array $set, array &$errors, string $base): array
    {
        $out = [];

        // price
        if (isset($set['price'])) {
            $price = (string)$set['price'];
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
                $errors[] = $this->err("$base.price", 'admin.import.errors.price_invalid', []);
            } else {
                $out['price'] = $price;
            }
        }

        if (isset($set['currency'])) {
            $out['currency'] = strtoupper($set['currency']);
        }

        if (isset($set['is_active'])) {
            $out['is_active'] = (bool)$set['is_active'];
        }

        if (isset($set['meta']) && is_array($set['meta'])) {
            $out['meta'] = $set['meta'];
        }

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
    private function normalizeTranslations(array $trs, array &$errors, string $base): array
    {
        $out = [];

        foreach ($trs as $locale => $data) {

            if (!in_array($locale, ['de', 'en', 'ru'], true)) {
                continue;
            }

            if (!is_array($data)) {
                continue;
            }

            $row = [];

            foreach (['title', 'description', 'details'] as $field) {
                if (isset($data[$field])) {
                    $row[$field] = trim(strip_tags($data[$field]));
                }
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
    private function err(string $path, string $key, array $params): array
    {
        return [
            'path' => $path,
            'message_key' => $key,
            'params' => $params,
        ];
    }
}
