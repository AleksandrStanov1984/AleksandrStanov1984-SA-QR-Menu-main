<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
       'section_id',
       'key',
       'sort_order',
       'price',
       'currency',
       'image_path',
       'meta',
       'is_active'
    ];

    protected $casts = [
       'meta' => 'array',
       'is_active' => 'boolean',
       'price' => 'decimal:2'
    ];

    public function section()
    {
       return $this->belongsTo(Section::class);
    }

    public function translations()
    {
       return $this->hasMany(ItemTranslation::class);
    }
}
