<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    /** @use HasFactory<\Database\Factories\SecurityEventFactory> */
    use HasFactory;

    protected $fillable = [
        'actor_id',
        'target_user_id',
        'restaurant_id',
        'event',
        'meta',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
