<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'meta',                 // ✅ NEW
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
        'meta' => 'array',      // ✅ NEW
        'is_active' => 'boolean',
        'trial_ends_at' => 'date',
        'monthly_price' => 'decimal:2',
    ];

    public function tokens()
    {
        return $this->hasMany(RestaurantToken::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
