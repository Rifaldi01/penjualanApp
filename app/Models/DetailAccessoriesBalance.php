<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailAccessoriesBalance extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function accessory()
    {
        return $this->belongsTo(Accessories::class, 'accessories_id');
    }

    public function balance()
    {
        return $this->belongsTo(AccessoriesBalance::class, 'balance_accessories_id');
    }
}
