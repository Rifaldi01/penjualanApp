<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $itemsByCategory = Item::with('cat')
            ->select('itemcategory_id',
                \DB::raw('count(*) as total'),
                \DB::raw('SUM(case when status = 0 then 1 else 0 end) as available')
            )
            ->groupBy('itemcategory_id')
            ->get();
        $item = Item::all()->count();
        $divisi = Divisi::all();
        $totaldivisiactive = Divisi::where('status', 'active')->count();
        $totaldivisi = Divisi::all()->count();
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin')
                ->where('name', '!=', 'manager');
        })->get();
        return view('manager.index', compact('user', 'itemsByCategory', 'item', 'divisi', 'totaldivisi', 'totaldivisiactive'));
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
}
