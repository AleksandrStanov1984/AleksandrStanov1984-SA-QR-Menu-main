<?php

namespace App\Models;

use App\Support\ImagePipeline\RestaurantAssetsService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
            'name',
            'slug',

            'template_key',
            'plan_key',

            'default_locale',
            'enabled_locales',

            'theme_tokens',

            'logo_path',
            'background_path',

            'custom_css',

            'meta',

            'is_active',

            // =========================
            // BILLING
            // =========================
            'trial_ends_at',
            'paid_until',

            'keep_data',
            'keep_data_until',

            'trial_used_at',

            // =========================
            // CONTACT
            // =========================
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

            // =========================
            // BILLING
            // =========================
            'trial_ends_at' => 'datetime',
            'paid_until' => 'datetime',

            'keep_data' => 'boolean',
            'keep_data_until' => 'datetime',

            'trial_used_at' => 'datetime',
        ];



    // =========================
    // RELATIONS
    // =========================
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

    public function qr()
    {
        return $this->hasOne(RestaurantQr::class);
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

    public function billingRecords()
    {
        return $this->hasMany(BillingRecord::class)
            ->latest('period_to');
    }

    // =========================
    // BOOT
    // =========================
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

    // =========================
    // FEATURES
    // =========================
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

    // =========================
    // PUBLIC SOCIALS
    // =========================
    public function publicSocialLinks()
    {
        return $this->socialLinks()
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    // =========================
    // BILLING HELPERS
    // =========================
    public function activeUntil(): ?\Carbon\Carbon
    {
        if (
            $this->paid_until &&
            $this->paid_until->isFuture()
        ) {
            return $this->paid_until;
        }

        if (
            $this->trial_ends_at &&
            $this->trial_ends_at->isFuture()
        ) {
            return $this->trial_ends_at;
        }

        return $this->paid_until
            ?: $this->trial_ends_at;
    }

    public function isBillingActive(): bool
    {
        return
            $this->is_active &&
            $this->activeUntil()?->isFuture();
    }

    public function billingDaysLeft(): ?int
    {
        $until = $this->activeUntil();

        if (!$until) {
            return null;
        }

        return (int) ceil(
            now()->floatDiffInDays($until, false)
        );
    }

    public function billingWarningLevel(): ?string
    {
        $days = $this->billingDaysLeft();

        if ($days === null) {
            return null;
        }

        if ($days <= 0) {
            return 'expired';
        }

        if ($days <= 5) {
            return 'danger';
        }

        if ($days <= 10) {
            return 'warning';
        }

        return 'ok';
    }

    public function isExpired(): bool
    {
        $until = $this->activeUntil();

        if (!$until) {
            return true;
        }

        return $until->isPast();
    }

    public function isTrial(): bool
    {
        return
            $this->trial_ends_at &&
            $this->trial_ends_at->isFuture() &&
            !$this->paid_until;
    }

    public function billingStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if (
            $this->paid_until &&
            $this->paid_until->isFuture()
        ) {
            return 'paid';
        }

        if (
            $this->trial_ends_at &&
            $this->trial_ends_at->isFuture()
        ) {
            return 'trial';
        }

        return 'expired';
    }

    // =========================
    // MERCHANT
    // =========================
    public function merchant(): object
    {
        $streetLine = trim(implode(' ', array_filter([
            $this->street,
            $this->house_number,
        ])));

        $cityLine = trim(implode(' ', array_filter([
            $this->postal_code,
            $this->city,
        ])));

        $address = trim(implode(', ', array_filter([
            $streetLine,
            $cityLine,
        ])));

        return (object) [
            'name' => $this->name,
            'address' => $address,
            'map_url' => 'https://maps.google.com/?q=' . urlencode($address),
            'phone_raw' => $this->phone,
            'phone_pretty' => $this->formatPhone($this->phone),
            'contact_email' => $this->contact_email,
        ];
    }

    private function formatPhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $clean = preg_replace('/[^\d+]/', '', $phone);

        if (!$clean) {
            return null;
        }

        if (str_starts_with($clean, '+49')) {

            $rest = substr($clean, 3);

            return '+49 '
                . substr($rest, 0, 3)
                . ' '
                . substr($rest, 3);
        }

        return $phone;
    }

    public function monthlyPrice(): ?float
    {
        return $this->plan?->price;
    }

    public function canBeActivated(): bool
    {
        if (
            $this->trial_ends_at &&
            $this->trial_ends_at->isFuture()
        ) {
            return true;
        }

        if (
            $this->paid_until &&
            $this->paid_until->isFuture()
        ) {
            return true;
        }

        return false;
    }

    public function canBePurged(): bool
    {
        if ($this->is_active) {
            return false;
        }

        if ($this->keep_data) {
            return false;
        }

        if (
            $this->paid_until &&
            $this->paid_until
                ->copy()
                ->addDays(31)
                ->isFuture()
        ) {
            return false;
        }

        if (
            !$this->paid_until &&
            $this->trial_ends_at &&
            $this->trial_ends_at
                ->copy()
                ->addDays(31)
                ->isFuture()
        ) {
            return false;
        }

        return true;
    }

    public function carouselConfig(): array
    {
        return data_get($this->meta, 'carousel', []);
    }
}
