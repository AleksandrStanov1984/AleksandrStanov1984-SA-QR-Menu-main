<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantSocialLink extends Model
{
    use SoftDeletes;

    protected $table = 'restaurant_social_links';

    protected $fillable = [
        'restaurant_id',
        'title',
        'url',
        'icon_path',
        'sort_order',
        'is_active',
        'deleted_by_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
