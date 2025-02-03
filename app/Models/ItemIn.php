<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemIn extends Model
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
}
