<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\ImagePipeline\RestaurantAssetsService;

class Restaurant extends Model
{

    public const PLAN_STARTER = 'starter';
    public const PLAN_BASIC = 'basic';
    public const PLAN_PRO = 'pro';

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
            ->where('is_active', true)
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

    public function hasFeature(string $feature): bool
    {
        return match ($feature) {

            'images' => in_array($this->plan_key, ['basic','pro']),
            'badges' => $this->plan_key === 'pro',
            'hours.modal' => $this->plan_key === 'pro',
            'hours.status' => in_array($this->plan_key, ['basic','pro']),

            default => false,
        };
    }

    public function plan()
    {
        return $this->belongsTo(MenuPlan::class, 'plan_key', 'key');
    }

    public function template()
    {
        return $this->belongsTo(MenuTemplate::class, 'template_key', 'key');
    }
}
