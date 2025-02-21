<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailAccessories extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }
    public function accessories()
    {
        return $this->belongsTo(Accessories::class,'accessories_id');
    }
}
