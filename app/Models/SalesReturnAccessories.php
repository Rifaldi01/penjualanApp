<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturnAccessories extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    public function accessoriesSale()
    {
        return $this->belongsTo(AccessoriesSale::class, 'accessories_sale_id')
            ->withTrashed();
    }

    public function accessories()
    {
        return $this->belongsTo(Accessories::class, 'accessories_id')
            ->withTrashed();
    }
}
