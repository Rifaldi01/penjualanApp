<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturnItem extends Model
{
    use HasFactory,  SoftDeletes;
    protected $guarded = [];
    public function itemSale()
    {
        return $this->belongsTo(ItemSale::class, 'item_sale_id')
            ->withTrashed();
    }

}
