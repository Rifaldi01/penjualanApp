<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function returnItems()
    {
        return $this->hasMany(SalesReturnItem::class, 'sale_return_id');
    }

    public function returnAccessories()
    {
        return $this->hasMany(SalesReturnAccessories::class, 'sale_return_id');
    }
}
