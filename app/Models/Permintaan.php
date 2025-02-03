<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function detailAccessories()
    {
        return $this->hasMany(DetailAccessories::class, 'permintaan_id');
    }
    public function divisiAsal()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id_asal');
    }

    public function divisiTujuan()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id_tujuan');
    }
}
