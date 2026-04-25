<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

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

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
       return $this->belongsTo(Restaurant::class);
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
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

    public function isCategory(): bool
    {
        return is_null($this->parent_id);
    }

    public function isSubcategory(): bool
    {
        return !is_null($this->parent_id);
    }

    protected static function booted()
    {
        static::deleting(function ($section) {

            if ($section->isForceDeleting()) {

                $section->items()->each(function ($item) {
                    $item->forceDelete();
                });

                $section->children()->each(function ($child) {
                    $child->forceDelete();
                });

                $section->translations()->delete();
            }
        });
    }
}
