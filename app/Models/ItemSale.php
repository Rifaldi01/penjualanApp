<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSale extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class, 'itemcategory_id', 'id');
    }
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
}
