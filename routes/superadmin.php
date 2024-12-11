<?php

use App\Http\Controllers\SuperAdmin\AccountController;
use Illuminate\Support\Facades\Route;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\SuperAdmin\DashboardController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth:web', 'role:superAdmin'], 'prefix' => 'superadmin'], function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('/account', AccountController::class)->names('superadmin.account');

    //account
    Route::resource('/account', AccountController::class)->names('superadmin.account');
    //account end
});
