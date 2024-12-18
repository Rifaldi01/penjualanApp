<?php

use Illuminate\Support\Facades\Route;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Gudang\DashboardController;
use App\Http\Controllers\Gudang\ItemCategoryController;
use App\Http\Controllers\Gudang\ItemController;
use App\Http\Controllers\Gudang\AccessoriesController;
use App\Http\Controllers\EditController;
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
Route::group(['middleware' => ['auth:web']], function () {
    Route::get('/', function () {
        return view('welcome');
    });
});
Auth::routes([
    'register' => false
]);

Route::group(['middleware' => ['auth:web']], function () {
    Route::resource('/profile/edit', EditController::class)->names('profile.edit');
});
Route::group(['middleware' => ['auth:web', 'role:gudang'], 'prefix' => 'gudang'], function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    //Item
    Route::resource('/item', ItemController::class)->names('gudang.item');
    Route::get('/items/{item}/download', [ItemController::class, 'download'])->name('items.download');
    Route::get('item-report', [ItemController::class, 'report'])->name('gudang.item.report');
    Route::get('/itmein', [ItemController::class, 'itemin'])->name('gudang.item.itemin');
    Route::get('/itmeout', [ItemController::class, 'itemout'])->name('gudang.item.itemout');
    Route::post('/item/print', [ItemController::class, 'print'])->name('gudang.item.print');

    //Item end

    //Item Category
    Route::resource('/category', ItemCategoryController::class)->names('gudang.cat');
    //Item Category end

    //accessories
    Route::resource('/access', AccessoriesController::class)->names('gudang.acces');
    Route::get('edit/access', [AccessoriesController::class, 'editMultiple'])->name('gudang.acces.editmultiple');
    Route::post('update/access', [AccessoriesController::class, 'updateMultiple'])->name('gudang.acces.updatemultiple');
    Route::post('check/access', [AccessoriesController::class, 'checkCode'])->name('gudang.acces.checkcode');
    Route::get('/acces/{acces}/download', [AccessoriesController::class, 'download'])->name('acces.download');
    Route::get('acces-report', [AccessoriesController::class, 'report'])->name('gudang.acces.report');
    Route::get('/accesin', [AccessoriesController::class, 'accesin'])->name('gudang.acces.accesin');
    Route::get('/accesout', [AccessoriesController::class, 'accesout'])->name('gudang.acces.accesout');
    Route::post('/acces/print', [AccessoriesController::class, 'print'])->name('gudang.acces.print');
    //accessories end

});
