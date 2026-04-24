<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\ImagePipeline\RestaurantAssetsService;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug',
        'template_key',
        'default_locale',
        'enabled_locales',
        'theme_tokens',
        'logo_path',
        'background_path',
        'custom_css',
        'meta',
        'is_active',
        'trial_ends_at',
        'plan_key',
        'monthly_price',
        'phone',
        'contact_name',
        'contact_email',
        'city',
        'street',
        'house_number',
        'postal_code',
    ];

    protected $casts = [
        'enabled_locales' => 'array',
        'theme_tokens' => 'array',
        'meta' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'date',
        'monthly_price' => 'decimal:2',
    ];

    public function token()
    {
        return $this->hasOne(RestaurantToken::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(RestaurantSocialLink::class)
            ->orderBy('sort_order');
    }

    public function hours()
    {
        return $this->hasMany(RestaurantHour::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected static function booted()
    {
        static::created(function ($restaurant) {

            app(RestaurantAssetsService::class)
                ->create($restaurant->id);

        });

        static::deleting(function ($restaurant) {

            app(RestaurantAssetsService::class)
                ->delete($restaurant->id);

        });
    }

    public function qr()
    {
        return $this->hasOne(RestaurantQr::class);
    }

    public function feature(string $key, $default = null)
    {
        return data_get(
            config("plan_features.{$this->plan_key}"),
            $key,
            $default
        );
    }

    public function hasFeature(string $feature): bool
    {
        return (bool) $this->feature($feature, false);
    }

    public function plan()
    {
        return $this->belongsTo(MenuPlan::class, 'plan_key', 'key');
    }

    public function template()
    {
        return $this->belongsTo(MenuTemplate::class, 'template_key', 'key');
    }

    public function banners()
    {
        return $this->hasMany(RestaurantBanner::class)
            ->orderBy('sort_order');
    }

    public function ogLimit(): ?int
    {
        return $this->feature('og_limit');
    }

    public function hasOgFeature(): bool
    {
        return $this->feature('og_images');
    }

    public function getOg(): array
    {
        return $this->meta['og'] ?? [];
    }

    public function publicSocialLinks()
    {
        return $this->socialLinks()
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
