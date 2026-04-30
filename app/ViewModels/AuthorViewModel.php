<?php

namespace App\ViewModels;

use App\Models\Restaurant;
use App\Services\ImageService;

class AuthorViewModel
{
    public Restaurant $restaurant;
    public string $locale;

    public array $socials = [];

    protected ImageService $images;

    public string $profileImage;
    public string $profileAlt;

    public function __construct(Restaurant $restaurant, string $locale)
    {
        $this->restaurant = $restaurant;
        $this->locale = $locale;

        app()->setLocale($locale);

        $this->images = app(ImageService::class);

        $this->profileImage = $this->images->authorProfile();
        $this->profileAlt = __('author.hero.photo_alt');

        $this->socials = $this->buildSocials();
    }

    private function buildSocials(): array
    {
        return collect(config('author.socials', []))
            ->filter(fn ($item) => !empty($item['url']))
            ->map(fn ($item) => [
                'key' => strtolower(trim($item['key'])),
                'url' => $item['url'],
                'icon' => $this->images->socialIcon(
                    null,
                    strtolower(trim($item['key']))
                ),
            ])
            ->values()
            ->toArray();
    }

    public function locales(): array
    {
        $enabled = $this->restaurant->enabled_locales ?? [];

        if (empty($enabled)) {
            return [$this->restaurant->default_locale ?? 'de'];
        }

        return array_values(array_unique($enabled));
    }
}
