<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'restaurant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
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

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

public function hasPerm(string $key): bool
{
    if ($this->is_super_admin)
        return true;

    $perms = $this->meta['permissions'] ?? [];

    return (bool)($perms[$key] ?? false);
}

public function setPerm(string $key, bool $value): void
{
    $meta = $this->meta ?? [];

    $perms = $meta['permissions'] ?? [];
    $perms[$key] = $value;

    $meta['permissions'] = $perms;

    $this->meta = $meta;
}

}
