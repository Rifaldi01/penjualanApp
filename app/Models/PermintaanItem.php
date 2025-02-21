<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function detailItem()
    {
        return $this->hasMany(DetailItem::class, 'permintaan_item_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
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
