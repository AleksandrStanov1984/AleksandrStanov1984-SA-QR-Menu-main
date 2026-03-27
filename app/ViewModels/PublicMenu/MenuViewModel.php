<?php

namespace App\ViewModels\PublicMenu;

use App\Models\Restaurant;
use App\Support\ImagePipeline\ImageService;
use App\DTO\ItemMetaDTO;
use Carbon\Carbon;

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

    public array $features = [];
    public bool $showHoursModal = false;
    public bool $showSearch = false;
    public bool $showSchedule = false;
    public bool $showStatus = false;
    public ?array $todayHours = null;

    public bool $showImages = false;
    public bool $showItemModal = false;

    public bool $showSpicy = false;
    public bool $showIsNew = false;
    public bool $showDishOfDay = false;

    public bool $showLongDescription = false;

    public array $featuredItems = [];

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
        $plan = $this->restaurant->plan;
        $features = $plan?->features ?? [];
        $this->features = $features;

        $this->showImages = $this->hasFeature($features, 'images');
        $this->showItemModal = $this->hasFeature($features, 'item_modal');
        $this->showSpicy = $this->hasFeature($features, 'spicy');
        $this->showIsNew = $this->hasFeature($features, 'is_new');
        $this->showDishOfDay = $this->hasFeature($features, 'dish_of_day');
        $this->showLongDescription = $this->hasFeature($features, 'long_description');
        $this->showSchedule   = true;
        $this->showStatus     = $this->hasFeature($features, 'status');
        $this->showSearch     = $this->hasFeature($features, 'search');
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

        $this->branding = [
            'logo'       => $this->images->url($restaurant->logo_path),
            'bg_light'   => $this->images->url($meta['bg_light'] ?? null),
            'bg_dark'    => $this->images->url($meta['bg_dark'] ?? null),
            'theme_mode' => $meta['theme_mode'] ?? 'light',
        ];

        $this->theme = $this->buildTheme();
        $this->featuredItems = $this->buildFeaturedItems();
        $this->footer = $this->buildFooter();

        $this->categories  = $this->buildCategoriesTree();
        $this->bestsellers = $this->buildBestsellers();

        $this->hours = $this->buildHours($restaurant->hours);

        $this->todayHours = $this->getTodayHours();

        $this->status = $this->detectStatus($this->todayHours);
        $this->showHoursModal = $this->hasFeature($features, 'hours_modal');
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

        $showImage = $this->showImages && ($metaDTO->showImage ?? true);

        return [
            'id' => $item->id,
            'title' => $this->translateItem($item, 'title'),
            'description' => $this->translateItem($item, 'description'),
            'details' => $this->showLongDescription
                ? $this->translateItem($item, 'details')
                : null,
            'price' => (float) $item->price,
            'currency' => $item->currency ?? 'EUR',
            'image' => $showImage
                ? $this->resolveImage($item->image_path)
                : null,
            'has_image' => $showImage && !empty($item->image_path),
            'sort_order' => $item->sort_order,
            'meta' => $metaDTO->toArray(),
            'ui' => [
                'spicy' => $this->showSpicy ? $metaDTO->spicy : null,
                'is_new' => $this->showIsNew ? $metaDTO->isNew : null,
                'dish_of_day' => $this->showDishOfDay ? $metaDTO->dishOfDay : null,
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
                    'icon' => $this->images->socialIcon($link->icon_path, $link->title),
                ];
            })
            ->values()
            ->toArray();

        return [
            'links' => $links,
        ];
    }

    private function buildFeaturedItems(): array
    {
        $items = $this->restaurant->sections
            ->flatMap(fn($s) => $s->items ?? collect())
            ->where('is_active', true);

        return $items
            ->filter(fn($i) => ItemMetaDTO::fromModel($i)->dishOfDay)
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

    private function hasFeature(array $features, string $key): bool
    {
        return !empty($features[$key]);
    }
    private function getTodayHours(): ?array
    {
        return collect($this->hours)->firstWhere('today', true);
    }

    protected function detectStatus(?array $todayHours): string
    {
        if (!$todayHours || !empty($todayHours['closed'])) {
            return 'closed';
        }

        $open = $todayHours['open'] ?? null;
        $close = $todayHours['close'] ?? null;

        if (!$open || !$close) {
            return 'closed';
        }

        $now = Carbon::now();

        $openAt = Carbon::parse($open);
        $closeAt = Carbon::parse($close);

        if ($now->lt($openAt) || $now->gte($closeAt)) {
            return 'closed';
        }

        if ($now->diffInMinutes($closeAt, false) <= 60) {
            return 'closing_soon';
        }

        return 'open';
    }

}
