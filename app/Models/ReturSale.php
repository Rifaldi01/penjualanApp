<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sales_id', 'id')->withTrashed();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

}
