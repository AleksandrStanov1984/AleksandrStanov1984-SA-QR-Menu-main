<?php

namespace App\ViewModels\PublicMenu;

use App\Models\Restaurant;
use App\Support\ImagePipeline\ImageService;

class MenuViewModel
{
    public string $templateKey;
    public string $locale = 'de';

    public string $status = 'open';

    public object $merchant;
    public array $branding = [];
    public array $theme = [];

    public array $categories = [];
    public array $bestsellers = [];

    public array $footer = [];
    public array $hours  = [];

    public function __construct(
        protected Restaurant $restaurant,
        string $locale
    ) {

        $this->locale = $locale ?: ($restaurant->default_locale ?? 'de');

        app()->setLocale($this->locale);

        $this->templateKey = $restaurant->template_key ?: 'united';

        $this->restaurant = $restaurant;
        $this->locale = $locale;
        $this->images = app(ImageService::class);

        $this->merchant = (object)[
            'name' => $restaurant->name,

            'address' => trim(
                $restaurant->street . ' ' .
                $restaurant->house_number . ', ' .
                $restaurant->postal_code . ' ' .
                $restaurant->city
            ),

            'map_url' =>
                'https://maps.google.com/?q=' .
                urlencode(
                    $restaurant->street . ' ' .
                    $restaurant->house_number . ', ' .
                    $restaurant->postal_code . ' ' .
                    $restaurant->city
                )
        ];

        $this->branding = [
            'logo'       => $restaurant->logo_path,
            'background' => $restaurant->background_path,
        ];

        $this->theme = $this->buildTheme();

        $this->footer = $this->buildFooter();

        $this->categories  = $this->buildCategoriesTree();
        $this->bestsellers = $this->buildBestsellers();

        $this->hours = $this->buildHours($restaurant->hours);

        $this->status = $this->detectStatus();
    }

    private function resolveImage(?string $path): ?string
    {
        if (!$path) {
            return config('images.urls.fallback');
        }

        return config('images.urls.assets') . '/' . ltrim($path, '/');
    }

    private function buildTheme(): array
    {
        $theme = [
            'primary' => $this->restaurant->primary_color ?? '#111827',
            'accent'  => $this->restaurant->accent_color ?? '#ff6600',
            'bg'      => $this->restaurant->bg_color ?? null,
            'card'    => $this->restaurant->card_color ?? '#ffffff',
            'muted'   => $this->restaurant->muted_color ?? '#f3f4f6',
            'text'    => $this->restaurant->text_color ?? '#111827',
        ];

        $tokens = $this->restaurant->theme_tokens;

        if (is_array($tokens)) {
            $theme = array_merge($theme, $tokens);
        }

        return $theme;
    }

    private function buildCategoriesTree(): array
    {
        $sections = $this->restaurant->sections
            ->where('is_active', true)
            ->sortBy('sort_order')
            ->values();

        $roots = $sections->whereNull('parent_id')->values();

        return $roots->map(function ($section) use ($sections) {

            $children = $sections
                ->where('parent_id', $section->id)
                ->values();

            return [

                'id' => $section->id,
                'slug' => $section->slug ?? null,
                'type' => $section->type,

                'layout' => $section->layout ?? null,

                'title'       => $this->translateSection($section, 'title'),
                'description' => $this->translateSection($section, 'description'),

                'image_url'   => $section->image_url ?? null,

                'subcategories' => $children->map(function ($sub) {

                    return [

                        'id' => $sub->id,
                        'slug' => $sub->slug ?? null,

                        'type' => $sub->type,
                        'layout' => $sub->layout ?? null,

                        'title'       => $this->translateSection($sub, 'title'),
                        'description' => $this->translateSection($sub, 'description'),

                        'image_url' => $sub->image_url ?? null,

                        'items' => $this->orderItemsForSection($sub->items),

                    ];

                })->toArray(),

                'items' => $this->orderItemsForSection($section->items),

            ];

        })->toArray();
    }

    private function buildBestsellers(): array
    {
        $items = $this->restaurant->sections
            ->flatMap(fn ($section) => $section->items ?? collect())
            ->where('is_active', true);

        return $items
            ->filter(fn ($i) => !empty(($i->meta ?? [])['bestseller_rank']))
            ->sortBy(fn ($i) => (int) (($i->meta ?? [])['bestseller_rank'] ?? 9999))
            ->map(fn ($i) => $this->mapItem($i))
            ->values()
            ->toArray();
    }

    private function mapItem($item): array
    {
        $meta = is_array($item->meta) ? $item->meta : [];

        $title = $this->translateItem($item, 'title');
        $price = (float) $item->price;

        return [

            'id' => $item->id,

            'title'       => $title,
            'description' => $this->translateItem($item, 'description'),
            'details'     => $this->translateItem($item, 'details'),

            'price' => $price,

            'currency' => $item->currency ?? 'EUR',

            'image_path' => $this->resolveImage($item->image_path),

            'sort_order' => $item->sort_order,

            'meta' => [

                'is_new' => (bool) ($meta['is_new'] ?? false),

                'dish_of_day' => (bool) ($meta['dish_of_day'] ?? false),

                'spicy_level' => (int) ($meta['spicy_level'] ?? 0),

            ],

        ];
    }

    private function translateSection($section, string $field): ?string
    {
        $tr = $section->translations
            ->firstWhere('locale', $this->locale);

        if ($tr && !empty($tr->{$field})) {
            return $tr->{$field};
        }

        $fallback = $section->translations
            ->firstWhere('locale', $this->restaurant->default_locale ?? 'de');

        return $fallback?->{$field};
    }

    private function translateItem($item, string $field): ?string
    {
        $tr = $item->translations
            ->firstWhere('locale', $this->locale);

        if ($tr && !empty($tr->{$field})) {
            return $tr->{$field};
        }

        $fallback = $item->translations
            ->firstWhere('locale', $this->restaurant->default_locale ?? 'de');

        return $fallback?->{$field};
    }

    private function buildFooter(): array
    {
        $links = $this->restaurant->socialLinks
            ->map(function ($link) {

                return [

                    'title' => $link->title,

                    'url'   => $link->url,

                    'icon'  => $link->icon_path
                        ? asset('storage/' . $link->icon_path)
                        : null,

                ];

            })
            ->values()
            ->toArray();

        return [

            'links' => $links,

            'featured_items' => $this->buildFooterFeaturedItems(),

        ];
    }

    private function buildFooterFeaturedItems(): array
    {
        $items = $this->restaurant->sections
            ->flatMap(fn($s) => $s->items ?? collect())
            ->where('is_active', true);

        return $items
            ->filter(fn($i) => (bool)(($i->meta ?? [])['is_new'] ?? false))
            ->sortBy(fn($i) => (int)($i->sort_order ?? 0))
            ->take(12)
            ->map(function ($i) {

                $mapped = $this->mapItem($i);

                $mapped['image_path'] = $this->resolveImage($i->image_path);

                return $mapped;

            })
            ->values()
            ->toArray();
    }

    private function orderItemsForSection($sectionItems): array
    {
        $items = collect($sectionItems)
            ->where('is_active', true)
            ->sortBy('sort_order')
            ->values();

        $newItem = $items->first(fn($i) => (bool)(($i->meta ?? [])['is_new'] ?? false));

        $dishItem = $items->first(fn($i) =>
            (bool)(($i->meta ?? [])['dish_of_day'] ?? false) &&
            (!$newItem || $i->id !== $newItem->id)
        );

        $ordered = collect();

        if ($newItem) $ordered->push($newItem);
        if ($dishItem) $ordered->push($dishItem);

        $ordered = $ordered->merge(
            $items->reject(fn($i) =>
                ($newItem && $i->id === $newItem->id) ||
                ($dishItem && $i->id === $dishItem->id)
            )
        );

        return $ordered->map(function ($item) use ($newItem, $dishItem) {

            $mapped = $this->mapItem($item);

            $mapped['display'] = [

                'is_new'      => $newItem ? ($item->id === $newItem->id) : false,

                'dish_of_day' => $dishItem ? ($item->id === $dishItem->id) : false,

            ];

            return $mapped;

        })->values()->toArray();
    }

    private function buildHours($hours): array
    {
        if (!$hours) return [];

        $days = [
            0 => __('menu.sunday'),
            1 => __('menu.monday'),
            2 => __('menu.tuesday'),
            3 => __('menu.wednesday'),
            4 => __('menu.thursday'),
            5 => __('menu.friday'),
            6 => __('menu.saturday'),
        ];

        $today = now()->dayOfWeek;

        return $hours->map(function ($row) use ($days, $today) {
            return [
                'label'  => $days[$row->day_of_week] ?? '',
                'open'   => \Carbon\Carbon::parse($row->open_time)->format('H:i'),
                'close'  => \Carbon\Carbon::parse($row->close_time)->format('H:i'),
                'closed' => (bool)($row->is_closed ?? false),
                'today'  => ($row->day_of_week ?? null) === $today,
            ];
        })->values()->toArray();
    }

    private function detectStatus(): string
    {
        $now = now();

        $today = $this->restaurant->hours
            ->firstWhere('day_of_week', $now->dayOfWeek);

        if (!$today || ($today->is_closed ?? false)) {
            return 'closed';
        }

        $open  = $today->open_time;
        $close = $today->close_time;

        if ($now->lt($open) || $now->gt($close)) {
            return 'closed';
        }

        if ($now->diffInMinutes($close) <= 60) {
            return 'closing_soon';
        }

        return 'open';
    }

    function vite_asset($path): string
    {
        return \Illuminate\Support\Facades\Vite::asset($path);
    }
}
