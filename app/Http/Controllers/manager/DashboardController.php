<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin')
                ->where('name', '!=', 'manager');
        })->get();
        return view('manager.index', compact('user'));
    }
}
