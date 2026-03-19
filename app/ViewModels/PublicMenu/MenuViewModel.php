<?php

namespace App\ViewModels\PublicMenu;

use App\Models\Restaurant;
use App\Support\ImagePipeline\ImageService;
use App\DTO\ItemMetaDTO;

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

    protected ImageService $images;

    public function __construct(
        protected Restaurant $restaurant,
        string $locale
    ) {
        $this->locale = $locale ?: ($restaurant->default_locale ?? 'de');

        app()->setLocale($this->locale);

        $this->templateKey = $restaurant->template_key ?: 'united';

        $this->restaurant = $restaurant;
        $this->images = app(ImageService::class);

        $meta = is_array($restaurant->meta) ? $restaurant->meta : [];

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

        // 🔥 BRANDING FIX
        $this->branding = [
            'logo'       => $this->images->url($restaurant->logo_path),
            'bg_light'   => $this->images->url($meta['bg_light'] ?? null),
            'bg_dark'    => $this->images->url($meta['bg_dark'] ?? null),
            'theme_mode' => $meta['theme_mode'] ?? 'light',
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
        return $this->images->url($path);
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
                'image_url'   => $this->resolveImage($section->image_path),

                'subcategories' => $children->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'slug' => $sub->slug ?? null,
                        'type' => $sub->type,
                        'layout' => $sub->layout ?? null,
                        'title'       => $this->translateSection($sub, 'title'),
                        'description' => $this->translateSection($sub, 'description'),
                        'image_url' => $this->resolveImage($sub->image_path),
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
            ->filter(fn ($i) => !empty((ItemMetaDTO::fromModel($i)->toArray()['bestseller_rank'] ?? null)))
            ->sortBy(fn ($i) => (int) ((ItemMetaDTO::fromModel($i)->toArray()['bestseller_rank'] ?? 9999)))
            ->map(fn ($i) => $this->mapItem($i))
            ->values()
            ->toArray();
    }

    private function mapItem($item): array
    {
        $metaDTO = ItemMetaDTO::fromModel($item);

        $showImage = $metaDTO->showImage ?? true;

        $imagePath = $showImage
            ? $this->resolveImage($item->image_path)
            : null;

        return [
            'id' => $item->id,
            'title'       => $this->translateItem($item, 'title'),
            'description' => $this->translateItem($item, 'description'),
            'details'     => $this->translateItem($item, 'details'),
            'price'    => (float) $item->price,
            'currency' => $item->currency ?? 'EUR',
            'image' => $imagePath,
            'has_image' => $showImage && !empty($item->image_path),
            'sort_order' => $item->sort_order,
            'meta' => $metaDTO->toArray(),
            'display' => [
                'is_new'       => $metaDTO->isNew,
                'dish_of_day'  => $metaDTO->dishOfDay,
                'show_image'   => $showImage,
            ],
        ];
    }

    private function buildFooter(): array
    {
        $links = $this->restaurant->socialLinks
            ->map(function ($link) {
                return [
                    'title' => $link->title,
                    'url'   => $link->url,
                    'icon'  => $this->images->url($link->icon_path),
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
            ->filter(fn($i) => ItemMetaDTO::fromModel($i)->isNew)
            ->sortBy(fn($i) => (int)($i->sort_order ?? 0))
            ->take(12)
            ->map(fn($i) => $this->mapItem($i))
            ->values()
            ->toArray();
    }

    private function orderItemsForSection($sectionItems): array
    {
        return collect($sectionItems)
            ->where('is_active', true)
            ->sortBy('sort_order')
            ->values()
            ->map(fn($item) => $this->mapItem($item))
            ->toArray();
    }

    private function translateSection($section, string $field): ?string
    {
        $tr = $section->translations->firstWhere('locale', $this->locale);

        if ($tr && !empty($tr->{$field})) return $tr->{$field};

        return $section->translations
            ->firstWhere('locale', $this->restaurant->default_locale ?? 'de')
            ?->{$field};
    }

    private function translateItem($item, string $field): ?string
    {
        $tr = $item->translations->firstWhere('locale', $this->locale);

        if ($tr && !empty($tr->{$field})) return $tr->{$field};

        return $item->translations
            ->firstWhere('locale', $this->restaurant->default_locale ?? 'de')
            ?->{$field};
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

        return collect($hours)->map(function ($row) use ($days, $today) {
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
        return 'open';
    }
}
