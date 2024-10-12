<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function accessoriesSales()
    {
        return $this->hasMany(AccessoriesSale::class);
    }
    public function accessories()
    {
        return $this->belongsToMany(Accessories::class, 'accessories_sales', 'sale_id', 'accessories_id')
            ->withPivot('qty');
    }

    public function itemSales()
    {
        return $this->hasMany(ItemSale::class, 'sale_id', 'id');
    }
}
