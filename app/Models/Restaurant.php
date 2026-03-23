<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\ImagePipeline\RestaurantAssetsService;

class Restaurant extends Model
{
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
}
