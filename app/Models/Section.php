<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
       'restaurant_id',
       'parent_id',
       'key',
       'sort_order',
       'type',
       'is_active',
       'title_font',
       'title_color',
    ];

    protected $casts = [
       'is_active' => 'boolean'
    ];

    public function restaurant()
    {
       return $this->belongsTo(Restaurant::class);
    }

    public function parent()
    {
        return $this->belongsTo(Section::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Section::class, 'parent_id');
    }

    public function items()
    {
       return $this->hasMany(Item::class);
    }

    public function translations()
    {
       return $this->hasMany(SectionTranslation::class);
    }
}
