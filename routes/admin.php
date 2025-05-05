<?php

use Illuminate\Support\Facades\Route;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\AccessoriesController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SupllierController;
use App\Http\Controllers\Admin\PembelianController;

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
Route::group(['middleware' => ['auth:web', 'role:admin'], 'prefix' => 'admin'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/', [DashboardController::class, 'index']);

    //customer
    Route::resource('/customer', CustomerController::class)->names('admin.customer');
    Route::post('/importexcel', [CustomerController::class, 'import'])->name('admin.importexcel');
    //customer end

    //item
    Route::resource('/item', ItemController::class)->names('admin.item');
    Route::get('/item-sale', [ItemController::class, 'sale'])->name('admin.item.sale');
    Route::get('/item-edit', [ItemController::class, 'editItem'])->name('admin.item.editItem');
    //ietm end

    //accessories
    Route::get('/accessories', [AccessoriesController::class, 'index'])->name('admin.acces.index');
    Route::get('/accessories/filter/{divisiId?}', [AccessoriesController::class, 'filterByDivisi']);
    Route::get('/acces-sale', [AccessoriesController::class, 'sale'])->name('admin.acces.sale');
    Route::get('/acces-edit', [AccessoriesController::class, 'editAcces'])->name('admin.acces.editAcces');
    Route::get('/acces-edit/{id}', [AccessoriesController::class, 'edit'])->name('admin.acces.edit');
    Route::put('/acces-edit/{id}', [AccessoriesController::class, 'update'])->name('admin.acces.update');
    //accessories end

    //sale
    Route::resource('/sale', SaleController::class)->names('admin.sale');
    Route::post('/fetch-data', [SaleController::class, 'fetchData'])->name('admin.sale.checkcode');
    //sale end
    //report
    Route::get('report', [ReportController::class, 'index'])->name('admin.report.index');
    Route::get('/report/filter', [ReportController::class, 'filter'])->name('report.filter');
    //report end

    //supplier
    Route::resource('supplier', SupllierController::class)->names('admin.supplier');
    //end supplier

    //pembelian
    Route::resource('pembelian', PembelianController::class)->names('admin.pembelian');
    Route::get('/pembelian/get-items-by-invoice/{kode_msk}', [PembelianController::class, 'loadItemsByInvoice']);
    //end pembelian


});
