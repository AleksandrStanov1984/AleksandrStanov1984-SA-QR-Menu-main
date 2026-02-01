<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemTranslation;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LanguageImportController extends Controller
{
    /**
     * Upload JSON with new language content and optionally set as default.
     * For now we accept a simple, explicit structure:
     * {
     *  "locale": "en",
     *  "sections": [
     *    {
     *      "key": "cold_appetizers",
     *      "title": "Cold Appetizers",
     *      "description": "...",
     *      "items": [
     *        {
     *          "key": "bruschetta",
     *          "title": "Bruschetta",
     *          "description": "...",
     *          "price": 6.5,
     *          "meta": {"short_description":"tomatoes, basil"}
     *        }
     *      ]
     *    }
     *  ]
     * }
     */
    public function import(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $request->user();

        if (!$user?->is_super_admin) {
            abort_unless((int) $user->restaurant_id === (int) $restaurant->id, 403);

            \App\Support\Permissions::abortUnless($user, 'import_manage');

        }

        $data = $request->validate([
            'locale' => ['required', 'string', 'max:5', 'regex:/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/'],
            'is_default' => ['sometimes', 'boolean'],
            'file' => ['required', 'file', 'mimes:json,txt'],
        ]);

        $locale = Str::lower(trim($data['locale']));

        // Store file for traceability.
        $filename = "menu_{$locale}_".now()->format('Ymd_His').".json";
        $path = Storage::disk('public')->putFileAs(
            "restaurants/{$restaurant->id}/imports",
            $request->file('file'),
            $filename
        );

        // Update enabled locales (always include DE)
        $enabled = $restaurant->enabled_locales ?: [];
        $enabled[] = 'de';
        $enabled[] = $locale;

        $restaurant->enabled_locales = array_values(array_unique(array_filter($enabled)));

        if (($data['is_default'] ?? false) === true) {
            $restaurant->default_locale = $locale;
        } else {
            // If nothing selected anywhere, default remains DE.
            $restaurant->default_locale = $restaurant->default_locale ?: 'de';
        }

        // safety: default must be enabled
        if (!in_array($restaurant->default_locale, $restaurant->enabled_locales, true)) {
            $restaurant->default_locale = 'de';
        }

        $restaurant->save();

        // Try to parse and upsert translations.
        try {
            $json = json_decode(
                file_get_contents(Storage::disk('public')->path($path)),
                true,
                flags: JSON_THROW_ON_ERROR
            );

            if (is_array($json)) {
                $this->applyMenuJson($restaurant, $locale, $json);
            }
        } catch (\Throwable $e) {
            // Keep silent - upload is still useful even if JSON isn't in expected format.
            return back()->with('status', "Language added (file stored). JSON import skipped: {$e->getMessage()}");
        }

        return back()->with('status', 'Language added and imported.');
    }

    public function setDefault(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $request->user();

        if (!$user?->is_super_admin) {
            abort_unless((int) $user->restaurant_id === (int) $restaurant->id, 403);
            // ✅ permission gate (user must have this right)
            abort_unless($user->hasPerm('import_manage'), 403);
        }

        $data = $request->validate([
            'default_locale' => ['required', 'string', 'max:5', 'regex:/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/'],
        ]);

        $locale = Str::lower(trim($data['default_locale']));
        $enabled = $restaurant->enabled_locales ?: ['de'];

        if (!in_array($locale, $enabled, true)) {
            return back()->withErrors(['default_locale' => 'Locale is not enabled for this restaurant.']);
        }

        $restaurant->default_locale = $locale;

        // safety: default must be enabled
        if (!in_array($restaurant->default_locale, $enabled, true)) {
            $restaurant->default_locale = 'de';
        }

        $restaurant->save();

        return back()->with('status', 'Default language updated.');
    }

    private function applyMenuJson(Restaurant $restaurant, string $locale, array $json): void
    {
        $sections = $json['sections'] ?? [];
        if (!is_array($sections)) {
            return;
        }

        foreach ($sections as $s) {
            if (!is_array($s)) {
                continue;
            }

            $sectionKey = isset($s['key']) ? Str::slug((string) $s['key'], '_') : null;
            if (!$sectionKey) {
                // fallback to slug of title
                $sectionKey = isset($s['title']) ? Str::slug((string) $s['title'], '_') : null;
            }
            if (!$sectionKey) {
                continue;
            }

            $section = Section::query()->firstOrCreate(
                ['restaurant_id' => $restaurant->id, 'key' => $sectionKey],
                ['type' => $s['type'] ?? 'food', 'sort_order' => (int) ($s['sort_order'] ?? 0)]
            );

            SectionTranslation::query()->updateOrCreate(
                ['section_id' => $section->id, 'locale' => $locale],
                ['title' => (string) ($s['title'] ?? $sectionKey), 'description' => $s['description'] ?? null]
            );

            $items = $s['items'] ?? [];
            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $it) {
                if (!is_array($it)) {
                    continue;
                }

                $itemKey = isset($it['key']) ? Str::slug((string) $it['key'], '_') : null;
                if (!$itemKey) {
                    $itemKey = isset($it['title']) ? Str::slug((string) $it['title'], '_') : null;
                }
                if (!$itemKey) {
                    continue;
                }

                $item = Item::query()->firstOrCreate(
                    ['section_id' => $section->id, 'key' => $itemKey],
                    [
                        'sort_order' => (int) ($it['sort_order'] ?? 0),
                        'price' => $it['price'] ?? null,
                        'currency' => $it['currency'] ?? 'EUR',
                        'meta' => $it['meta'] ?? null,
                        'is_active' => (bool) ($it['is_active'] ?? true),
                    ]
                );

                // update basic props if present
                $item->price = array_key_exists('price', $it) ? $it['price'] : $item->price;
                $item->currency = $it['currency'] ?? $item->currency;

                if (array_key_exists('meta', $it)) {
                    $item->meta = $it['meta'];
                }

                $item->save();

                ItemTranslation::query()->updateOrCreate(
                    ['item_id' => $item->id, 'locale' => $locale],
                    ['title' => (string) ($it['title'] ?? $itemKey), 'description' => $it['description'] ?? null]
                );
            }
        }
    }
}
