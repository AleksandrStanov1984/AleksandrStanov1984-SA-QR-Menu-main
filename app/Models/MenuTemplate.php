<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
