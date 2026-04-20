<?php

namespace App\ViewModels;

class PublicMenuViewModel
{
    public function build($restaurant, string $locale): array
    {
        return [
            'restaurant' => $restaurant,
            'locale' => $locale,
            'theme' => $this->theme($restaurant),
            'sections' => $this->sections($restaurant, $locale),
        ];
    }

    protected function theme($restaurant): array
    {
        return [
            'primary' => $restaurant->primary_color ?? '#000000',
            'accent'  => $restaurant->accent_color ?? '#ff6600',
            'background' => $restaurant->background_color ?? null,
        ];
    }

    protected function sections($restaurant, $locale): array
    {
        return $restaurant->sections
            ->where('is_active', true)
            ->sortBy('sort_order')
            ->map(function ($section) use ($locale, $restaurant) {

                $translation = $this->resolveTranslation(
                    $section->translations,
                    $locale,
                    $restaurant
                );

                return [
                    'id' => $section->id,
                    'name' => $translation->title ?? null,
                    'description' => $translation->description ?? null,
                    'image' => $section->image_path,

                    'items' => $section->items
                        ->where('is_active', true)
                        ->sortBy(function ($item) {
                            return $item->bestseller_rank ?? 9999;
                        })
                        ->map(function ($item) use ($locale, $restaurant) {

                            $translation = $this->resolveTranslation(
                                $item->translations,
                                $locale,
                                $restaurant
                            );

                            return [
                                'id' => $item->id,
                                'title' => $translation->title ?? null,
                                'description' => $translation->description ?? null,
                                'image' => $item->image_path,

                                'price_cents' => $item->price_cents,
                                'tax_rate_percent' => $item->tax_rate_percent,
                                'bestseller_rank' => $item->bestseller_rank,

                                'is_new' => $item->is_new,
                                'is_spicy' => $item->is_spicy,
                                'dish_of_day' => $item->dish_of_day,

                                'variant_groups' => $item->variant_groups ?? [],
                            ];
                        })
                        ->values()
                ];
            })
            ->values()
            ->toArray();
    }

    protected function resolveTranslation($translations, $locale, $restaurant)
    {
        $enabled = $restaurant->enabled_locales ?? [];

        if (empty($enabled)) {
            $enabled = [$restaurant->default_locale ?? 'de'];
        }

        if (in_array($locale, $enabled, true)) {
            $t = $translations->firstWhere('locale', $locale);
            if ($t) return $t;
        }

        $default = $restaurant->default_locale ?? 'de';

        $t = $translations->firstWhere('locale', $default);
        if ($t) return $t;

        return $translations->first();
    }

    public function locales(): array
    {
        $locales = $this->restaurant->enabled_locales ?? [];

        if (empty($locales)) {
            return [$this->restaurant->default_locale ?? 'de'];
        }

        return $locales;
    }
}
