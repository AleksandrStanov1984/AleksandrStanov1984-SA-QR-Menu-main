<?php

namespace App\ViewModels\PublicMenu;

use App\DTO\ItemMetaDTO;
use App\Models\Restaurant;

use Carbon\Carbon;
use App\Support\Hours;
use App\Services\ImageService;

use Illuminate\Support\Facades\File;

class MenuViewModel
{
    public string $templateKey;

    public Restaurant $restaurant;

    public string $locale = 'de';

    public string $status = 'open';
    public object $merchant;

    public array $branding = [];

    public array $theme = [];

    public array $categories = [];
    public array $bestsellers = [];

    public array $footer = [];

    public array $hours = [];

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

    public bool $bestseller = false;
    public bool $showLongDescription = false;
    public array $featuredItems = [];
    public array $promoBanners = [];

    protected ImageService $images;
    public array $carouselItems = [];
    public string $ogImage;
    public string $ogTitle;
    public string $ogDescription;
    public string $ogUrl;

    public function __construct(
        Restaurant $restaurant,
        string $locale
    ) {
        $this->locale = $locale ?: ($restaurant->default_locale ?? 'de');

        app()->setLocale($this->locale);

        $this->templateKey = $restaurant->template_key ?: 'united';
        $this->restaurant = $restaurant;
        $this->images = app(ImageService::class);

        $features = config("plan_features.{$this->restaurant->plan_key}") ?? [];
        $this->features = $features;

        $this->showImages = $this->hasFeature($features, 'images');
        $this->showItemModal = $this->hasFeature($features, 'item_modal');
        $this->showSpicy = $this->hasFeature($features, 'spicy');
        $this->showIsNew = $this->hasFeature($features, 'is_new');
        $this->showDishOfDay = $this->hasFeature($features, 'dish_of_day');
        $this->bestseller = $this->hasFeature($features, 'bestseller');
        $this->showLongDescription = $this->hasFeature($features, 'long_description');
        $this->showSchedule = true;
        $this->showStatus = $this->hasFeature($features, 'status');
        $this->showSearch = $this->hasFeature($features, 'search');

        $meta = is_array($restaurant->meta) ? $restaurant->meta : [];

        $streetLine = trim(implode(' ', array_filter([
            $restaurant->street,
            $restaurant->house_number,
        ])));

        $cityLine = trim(implode(' ', array_filter([
            $restaurant->postal_code,
            $restaurant->city,
        ])));

        $address = trim(implode(', ', array_filter([
            $streetLine,
            $cityLine,
        ])));

        $this->merchant = (object) [
            'name' => $restaurant->name,
            'address' => $address,
            'map_url' => 'https://maps.google.com/?q=' . urlencode($address),
        ];

        $this->branding = [
            'logo' => !empty($restaurant->logo_path)
                ? $this->images->url($restaurant->logo_path)
                : null,

            'bg_light' => !empty($meta['bg_light'] ?? null)
                ? $this->images->url($meta['bg_light'])
                : null,

            'bg_dark' => !empty($meta['bg_dark'] ?? null)
                ? $this->images->url($meta['bg_dark'])
                : null,

            'theme_mode' => $meta['theme_mode'] ?? 'light',
        ];

        $this->theme = $this->buildTheme();
        $this->featuredItems = $this->buildFeaturedItems();
        $this->promoBanners = $this->buildPromoBanners();
        $this->footer = $this->buildFooter();

        $this->categories = $this->buildCategoriesTree();
        $this->bestsellers = $this->buildBestsellers();

        $this->hours = $this->buildHours($restaurant->hours);
        $this->todayHours = $this->getTodayHours();

        $this->status = Hours::status($this->todayHours);
        $this->showHoursModal = $this->hasFeature($features, 'hours_modal');

        $this->carouselItems = $this->buildCarousel();

        $this->ogImage = $this->images
            ->ogForLocale($restaurant, $this->locale);

        $this->ogTitle = $restaurant->name;

        $this->ogDescription = $this->buildOgDescription($restaurant);

        $this->ogUrl = request()->fullUrl();
    }

    private function resolveImage(?string $path): ?string
    {
        return $this->images->url($path);
    }

    private function buildTheme(): array
    {
        $theme = [
            'primary' => $this->restaurant->primary_color ?? '#111827',
            'accent' => $this->restaurant->accent_color ?? '#ff6600',
            'bg' => $this->restaurant->bg_color ?? null,
            'card' => $this->restaurant->card_color ?? '#ffffff',
            'muted' => $this->restaurant->muted_color ?? '#f3f4f6',
            'text' => $this->restaurant->text_color ?? '#111827',
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

        $grouped = $sections->groupBy('parent_id');
        $roots = $grouped->get(null, collect())->values();

        return $roots->map(function ($section) use ($grouped) {
            $children = $grouped->get($section->id, collect())->values();

            return [
                'id' => $section->id,
                'slug' => $section->slug ?? null,
                'type' => $section->type,
                'layout' => $section->layout ?? null,
                'title' => $this->translateSection($section, 'title'),
                'description' => $this->translateSection($section, 'description'),
                'image_url' => $this->resolveImage($section->image_path),

                'subcategories' => $children->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'slug' => $sub->slug ?? null,
                        'type' => $sub->type,
                        'layout' => $sub->layout ?? null,
                        'title' => $this->translateSection($sub, 'title'),
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
            ->where('is_active', true)
            ->map(function ($item) {
                $item->meta_dto = ItemMetaDTO::fromModel($item);
                return $item;
            });

        return $items
            ->filter(fn ($i) => !empty($i->meta_dto->bestseller_rank))
            ->sortBy(fn ($i) => (int) ($i->meta_dto->bestseller_rank ?? 9999))
            ->map(fn ($i) => $this->mapItem($i))
            ->values()
            ->toArray();
    }

    private function mapItem($item): array
    {
        $metaDTO = $item->meta_dto ?? ItemMetaDTO::fromModel($item);

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
                ? $this->images->food($item->image_path)
                : null,
            'has_image' => $showImage && !empty($item->image_path),
            'sort_order' => $item->sort_order,
            'meta' => $metaDTO->toArray(),
            'ui' => [
                'spicy' => $this->showSpicy ? $metaDTO->spicy : null,
                'is_new' => $this->showIsNew ? $metaDTO->isNew : null,
                'dish_of_day' => $this->showDishOfDay ? $metaDTO->dishOfDay : null,
                'bestseller' => $this->bestseller ? $metaDTO->bestseller : null,
            ],
            'show_image_block' => $showImage,
        ];
    }

    private function buildFooter(): array
    {
        $links = $this->restaurant->socialLinks
            ->map(function ($link) {
                return [
                    'title' => $link->title,
                    'url' => $link->url,
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
            ->flatMap(fn ($s) => $s->items ?? collect())
            ->where('is_active', true)
            ->map(function ($item) {
                $item->meta_dto = $item->meta_dto ?? ItemMetaDTO::fromModel($item);
                return $item;
            });

        return $items
            ->filter(fn ($i) => $i->meta_dto->dishOfDay)
            ->sortBy(fn ($i) => (int) ($i->sort_order ?? 0))
            ->take(12)
            ->map(fn ($i) => $this->mapItem($i))
            ->values()
            ->toArray();
    }

    private function orderItemsForSection($sectionItems): array
    {
        return collect($sectionItems)
            ->where('is_active', true)
            ->sortBy('sort_order')
            ->values()
            ->map(function ($item) {
                $item->meta_dto = $item->meta_dto ?? ItemMetaDTO::fromModel($item);
                return $this->mapItem($item);
            })
            ->toArray();
    }

    private function translateSection($section, string $field): ?string
    {
        $tr = $this->resolveTranslation(
            $section->translations,
            $this->locale,
            $this->restaurant
        );

        return $tr->{$field} ?? null;
    }

    private function translateItem($item, string $field): ?string
    {
        $tr = $this->resolveTranslation(
            $item->translations,
            $this->locale,
            $this->restaurant
        );

        return $tr->{$field} ?? null;
    }

    private function buildHours($hours): array
    {
        if (!$hours) {
            return [];
        }

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
                'day_of_week' => (int) ($row->day_of_week ?? -1),
                'label' => $days[$row->day_of_week] ?? '',
                'open' => $row->open_time ? Carbon::parse($row->open_time)->format('H:i') : null,
                'close' => $row->close_time ? Carbon::parse($row->close_time)->format('H:i') : null,
                'closed' => (bool) ($row->is_closed ?? false),
                'today' => (int) ($row->day_of_week ?? -1) === $today,
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

    private function buildPromoBanners(): array
    {
        if (!$this->restaurant->feature('banners')) {
            return [];
        }

        $banners = $this->restaurant->relationLoaded('banners')
            ? $this->restaurant->banners
            : $this->restaurant->banners()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(5)
                ->get();

        $filtered = $banners
            ->filter(fn ($b) => $b->is_active && !empty($b->image_path));

        if ($filtered->isEmpty()) {
            return [];
        }

        return $filtered
            ->sortBy('sort_order')
            ->take(5)
            ->map(fn ($b) => [
                'id' => $b->id,
                'image' => $this->images->banner($b->image_path),
            ])
            ->values()
            ->toArray();
    }

    private function buildCarousel(): array
    {
        $meta = is_array($this->restaurant->meta ?? null)
            ? $this->restaurant->meta
            : [];

        // выключено
        if (!($meta['carousel_enabled'] ?? false)) {
            return [];
        }

        // feature check
        if (!$this->restaurant->feature('carousel')) {
            return [];
        }

        $source = $meta['carousel_source'] ?? 'bestseller';

        $items = $this->restaurant->sections
            ->flatMap(fn ($s) => $s->items ?? collect())
            ->where('is_active', true)
            ->map(function ($item) {
                $item->meta_dto = $item->meta_dto ?? ItemMetaDTO::fromModel($item);
                return $item;
            });

        // source selection
        $filtered = match ($source) {
            'is_new' => $items->filter(fn ($i) => $i->meta_dto->isNew === true),
            'dish_of_day' => $items->filter(fn ($i) => $i->meta_dto->dishOfDay === true),
            default => $items->filter(fn ($i) => $i->meta_dto->bestseller === true),
        };

        // limit by feature
        $limit = match (true) {
            $this->restaurant->feature('carousel_advanced') => 30,
            $this->restaurant->feature('carousel') => 15,
            default => 0,
        };

        if ($limit <= 0) {
            return [];
        }

        $result = $filtered
            ->sortBy(fn ($i) => (int) ($i->sort_order ?? 0))
            ->take($limit)
            ->map(fn ($i) => $this->mapItem($i))
            ->values();

        if ($result->isEmpty()) {
            return [];
        }

        if ($result->count() < 5) {
            $original = $result->values();

            while ($result->count() < 5) {
                foreach ($original as $item) {
                    $result->push($item);

                    if ($result->count() >= 5) {
                        break;
                    }
                }
            }
        }

        return $result->toArray();
    }

    public function locales(): array
    {
        $restaurant = $this->restaurant;

        if (!$restaurant) {
            return ['de'];
        }

        $enabled = $restaurant->enabled_locales ?? [];

        if (empty($enabled)) {
            return [$restaurant->default_locale ?? 'de'];
        }

        return array_values(array_unique($enabled));
    }

    private function buildOgDescription(Restaurant $restaurant): string
    {
        $desc = $restaurant->description ?? '';

        if (!$desc) {
            return __('menu.default_og_description', [
                'name' => $restaurant->name
            ]);
        }

        return mb_strimwidth(strip_tags($desc), 0, 160, '...');
    }

    protected function resolveTranslation($translations, $locale, $restaurant)
    {
        $enabled = $restaurant->enabled_locales ?? [];

        if (empty($enabled)) {
            $enabled = [$restaurant->default_locale ?? 'de'];
        }

        if (in_array($locale, $enabled, true)) {
            $t = $translations->firstWhere('locale', $locale);
            if ($t && !empty($t)) {
                return $t;
            }
        }

        $default = $restaurant->default_locale ?? 'de';

        $t = $translations->firstWhere('locale', $default);
        if ($t && !empty($t)) {
            return $t;
        }

        return $translations->first();
    }

    public function restaurant(): Restaurant
    {
        return $this->restaurant;
    }
}
