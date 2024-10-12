<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoriesIn extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function accessories()
    {
        return $this->belongsTo(Accessories::class, 'accessories_id', 'id');
    }
}
