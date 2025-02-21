<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function permintaanItem()
    {
        return $this->belongsTo(PermintaanItem::class, 'permintaan_item_id');
    }
    public function itemIn()
    {
        return $this->belongsTo(ItemIn::class, 'item_in_id');
    }

}
