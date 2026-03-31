<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantBanner extends Model
{
    protected $fillable = [
        'restaurant_id',
        'slot',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'slot' => 'integer',
        'sort_order' => 'integer',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
