<?php

use Illuminate\Support\Facades\Route;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Gudang\DashboardController;
use App\Http\Controllers\Gudang\ItemCategoryController;
use App\Http\Controllers\Gudang\ItemController;
use App\Http\Controllers\Gudang\AccessoriesController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\Gudang\PermintaanController;
use App\Http\Controllers\Gudang\PermintaanItemController;
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
    Route::post('reject/item/{id}', [ItemController::class, 'reject'])->name('gudang.item.reject');
    Route::post('redy/item/{id}', [ItemController::class, 'redy'])->name('gudang.item.redy');
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
    Route::get('reject/acces/', [AccessoriesController::class, 'kembali'])->name('gudang.acces.kembali');
    Route::post('reject/acces/', [AccessoriesController::class, 'reject'])->name('gudang.acces.reject');
    Route::get('reject/', [AccessoriesController::class, 'listReject'])->name('gudang.acces.listreject');
    //accessories end

    //permintaan accessories
    Route::resource('permintaan', PermintaanController::class)->names('gudang.permintaan');
    Route::put('/permintaan/{id}/approve', [PermintaanController::class, 'approve'])->name('gudang.permintaan.approve');
    Route::put('/permintaan/{id}/receive', [PermintaanController::class, 'receive'])->name('gudang.permintaan.receive');
    Route::get('/konfirmasi', [PermintaanController::class, 'konfirmasi'])->name('gudang.permintaan.konfirmasi');
    Route::get('/minta/accessories/{divisi_id}', [PermintaanController::class, 'fetchAccessories'])->name('gudang.permintaan.fetchAccessories');
    // end

    //permintaan Item
    Route::resource('permintaan-item', PermintaanItemController::class)->names('gudang.permintaanitem');
    Route::put('/permintaan-item/{id}/approve', [PermintaanItemController::class, 'approve'])->name('gudang.permintaanitem.approve');
    Route::put('/permintaan-item/{id}/receive', [PermintaanItemController::class, 'receive'])->name('gudang.permintaanitem.receive');
    Route::get('/konfirmasi-item', [PermintaanItemController::class, 'konfirmasi'])->name('gudang.permintaanitem.konfirmasi');
    Route::get('/minta/item/{divisi_id}', [PermintaanItemController::class, 'fetchAccessories'])->name('gudang.permintaanitem.fetchAccessories');



});
