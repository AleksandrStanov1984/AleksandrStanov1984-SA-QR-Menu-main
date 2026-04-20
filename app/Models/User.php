<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'restaurant_id',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'meta' => 'array',
        ];
    }

    /**
     * Relations
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Permissions (temporary always true)
     */
    public function hasPerm(string $key): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return true;
    }

    public function setPerm(string $key, bool $value): void
    {
        $meta = $this->meta ?? [];

        $perms = $meta['permissions'] ?? [];
        $perms[$key] = $value;

        $meta['permissions'] = $perms;

        $this->meta = $meta;
    }

    /**
     * Helpers
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /**
     * Get user name (без всякой логики)
     */
    public function getName(): string
    {
        if (!is_null($this->name)) {
            return (string) $this->name;
        }

        return (string) static::query()
            ->whereKey($this->getKey())
            ->value('name');
    }
}
