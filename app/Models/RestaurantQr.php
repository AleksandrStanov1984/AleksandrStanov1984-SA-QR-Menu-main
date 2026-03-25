<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantQr extends Model
{
    use HasFactory;

    protected $table = 'restaurant_qr';

    protected $fillable = [
        'restaurant_id',
        'qr_path',
        'logo_path',
        'background_path',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
