<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin');
        })->get();
        return view('superadmin.index', compact('user'));
    }
}
