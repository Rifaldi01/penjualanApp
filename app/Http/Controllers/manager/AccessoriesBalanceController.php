<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesBalance;
use App\Models\DetailAccessoriesBalance;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessoriesBalanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {

        $balances = AccessoriesBalance::with('divisi')
            ->orderByDesc('year')
            ->orderByDesc('divisi_id')
            ->get();

        $divisi = Divisi::all();

        return view('manager.balance.index', compact('divisi', 'balances'));
    }

    /*
    |--------------------------------------------------------------------------
    | DATA LIST
    |--------------------------------------------------------------------------
    */
    public function data(Request $request)
    {
        $query = AccessoriesBalance::with('divisi')
            ->orderByDesc('year');

        if ($request->divisi_id) {
            $query->where('divisi_id', $request->divisi_id);
        }

        return response()->json([
            'data' => $query->get()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $balance = AccessoriesBalance::with([
            'divisi',
            'details.accessory'
        ])->findOrFail($id);

        return view('manager.balance.show', compact('balance'));
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE YEARLY (FINAL VERSION)
    |--------------------------------------------------------------------------
    */
}
