<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Accessories extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function accessoriesSale()
    {
        return $this->hasMany(AccessoriesSale::class);
    }

    public function scopeWithSubtotal($query)
    {
        return $query->select(
            'accessories.id',
            'accessories.name',
            'accessories.price',
            'accessories.stok',
            'accessories.code_acces',
            DB::raw('SUM(accesories_categories.qty * accessories.price) as subtotal')
        )
            ->join('accesories_categories', 'accessories.id', '=', 'accesories_categories.accessories_id')
            ->groupBy(
                'accessories.id',
                'accessories.id',
                'accessories.name',
                'accessories.price',
                'accessories.stok',
                'accessories.code_acces'
            );
    }
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
    public function accessoriesIn()
    {
        return $this->hasMany(AccessoriesIn::class, 'accessories_id');
    }
}
