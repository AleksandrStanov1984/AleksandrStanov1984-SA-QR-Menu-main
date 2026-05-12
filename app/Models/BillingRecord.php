<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingRecord extends Model
{
    use HasFactory;

    // =========================
    // TYPES
    // =========================
    public const TYPE_TRIAL = 'trial';

    public const TYPE_PAYMENT = 'payment';

    public const TYPE_EXPIRATION = 'expiration';

    public const TYPE_MANUAL_EXTENSION = 'manual_extension';

    public const TYPE_DEACTIVATION = 'deactivation';

    public const TYPE_RESUME = 'resume';

    // =========================
    // STATUSES
    // =========================
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_SYSTEM = 'system';

    protected $fillable = [
        'restaurant_id',
        'confirmed_by',

        'type',
        'status',

        'plan_key',

        'amount',
        'currency',

        'period_from',
        'period_to',

        'confirmed_at',

        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',

        'period_from' => 'datetime',
        'period_to' => 'datetime',

        'confirmed_at' => 'datetime',
    ];

    // =========================
    // RELATIONS
    // =========================
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // =========================
    // HELPERS
    // =========================
    public function isTrial(): bool
    {
        return $this->type === self::TYPE_TRIAL;
    }

    public function isPayment(): bool
    {
        return $this->type === self::TYPE_PAYMENT;
    }

    public function isExpiration(): bool
    {
        return $this->type === self::TYPE_EXPIRATION;
    }

    public function isManualExtension(): bool
    {
        return $this->type === self::TYPE_MANUAL_EXTENSION;
    }

    public function isDeactivation(): bool
    {
        return $this->type === self::TYPE_DEACTIVATION;
    }

    public function isResume(): bool
    {
        return $this->type === self::TYPE_RESUME;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isSystem(): bool
    {
        return $this->status === self::STATUS_SYSTEM;
    }
}
