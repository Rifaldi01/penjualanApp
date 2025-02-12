<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function cat()
    {
        return $this->belongsTo(ItemCategory::class, 'itemcategory_id', 'id');
    }
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
    public function itemIn()
    {
        return $this->hasOne(ItemIn::class, 'no_seri', 'no_seri');
    }

}
