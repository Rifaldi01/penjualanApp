<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoriesSale extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
    public function accessories()
    {
        return $this->belongsTo(Accessories::class, 'accessories_id', 'id');
    }
}
