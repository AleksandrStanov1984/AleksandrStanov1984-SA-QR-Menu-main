<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

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
