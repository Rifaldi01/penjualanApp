<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin');
        })->get();
        $columns = Schema::getColumnListing('sales');

        $sale = Sale::where(function ($query) use ($columns) {
            foreach ($columns as $column) {
                $wrapped = DB::getQueryGrammar()->wrap($column);

                $query->orWhereRaw("LOWER(CAST($wrapped AS CHAR)) = ?", ['nan']);
            }
        })->get();
        return view('superadmin.index', compact('user', 'sale'));
    }
    public function userStatus()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin');
        })->get();

        $data = $users->map(function ($user) {
            return [
                'id'     => $user->id,
                'online' => $user->isOnline(),
            ];
        });

        return response()->json($data);
    }
    public function updateError(Request $request, $id)
    {
        $request->validate([
            'nominal_in' => 'nullable'
        ]);

        $sale = Sale::findOrFail($id);

        $sale->update([
            'total_price' => $request->total_price,
            'ppn'         => $request->ppn,
            'pph'         => $request->pp,
            'diskon'      => $request->diskon,
            'ongkir'      => $request->ongkir,
            'admin_fee'   => $request->admin_fee,
            'pay'         => $request->pay,
            'nominal_in'  => $request->nominal_in,
        ]);

        return back()->with('success', 'Data berhasil diperbarui.');
    }
}
