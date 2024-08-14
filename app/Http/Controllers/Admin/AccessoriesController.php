<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesSale;
use Illuminate\Http\Request;

class AccessoriesController extends Controller
{
    public function index()
    {
        $acces = Accessories::all();
        return view('admin.accessories.index', compact('acces'));
    }
    public function sale()
    {
        $sale = AccessoriesSale::with('sale', 'accessories')->get();
        return view('admin.accessories.sale', compact('sale'));
    }
}
