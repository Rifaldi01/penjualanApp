<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function sale()
    {
        return $this->hasMany(Sale::class);
    }
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
}
