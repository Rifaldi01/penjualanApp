<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function ItemBeli()
    {
        return $this->hasMany(ItemBeli::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
    public function itemin()
    {
        return $this->belongsTo(ItemIn::class);
    }
    public function accessoriesin()
    {
        return $this->belongsTo(AccessoriesIn::class);
    }
}
