<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTranslation extends Model
{
    protected $fillable = [
       'item_id',
       'locale',
       'title',
       'description',
       'details',
    ];

    public function item()
    {
       return $this->belongsTo(Item::class);
    }
}
