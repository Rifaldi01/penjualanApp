<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessoriesBalance extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
    public function details()
    {
        return $this->hasMany(DetailAccessoriesBalance::class, 'balance_accessories_id');
    }


}
