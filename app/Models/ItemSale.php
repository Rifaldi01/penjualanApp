<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSale extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class, 'itemcategory_id', 'id');
    }
}
