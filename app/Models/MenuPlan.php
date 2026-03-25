<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'price',
        'description',
        'is_active',
        'is_public',
        'features',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'features' => 'array',
    ];
}
