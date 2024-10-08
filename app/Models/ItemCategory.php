<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function item()
    {
        return $this->hasMany(Item::class, 'itemcategory_id', 'id');
    }
}
