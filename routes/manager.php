<?php

use Illuminate\Support\Facades\Route;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\manager\DashboardController;
use App\Http\Controllers\manager\SaleController;
use App\Http\Controllers\manager\ItemController;
use App\Http\Controllers\manager\ItemCategoryController;
use App\Http\Controllers\manager\CustomerController;
use App\Http\Controllers\manager\AccesoriesController;
use App\Http\Controllers\manager\ReportController;
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

Route::group(['middleware' => ['auth:web', 'role:manager'], 'prefix' => 'manager'], function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('manager.index');

    //sale
    Route::resource('/sale', SaleController::class)->names('manager.sale');
    Route::put('/bayar/{id}', [SaleController::class, 'bayar'])->name('manager.sale.bayar');
    Route::post('/fetch-data', [SaleController::class, 'fetchData'])->name('manager.sale.checkcode');
    //end sale

    //item
    Route::resource('/item', ItemController::class)->names('manager.item');
    Route::get('/items/{item}/download', [ItemController::class, 'download'])->name('manager.items.download');
    Route::get('item-report', [ItemController::class, 'report'])->name('manager.item.report');
    //item end

    //Item Category
    Route::resource('/category', ItemCategoryController::class)->names('manager.cat');
    //Item Category end

    //customer
    Route::resource('/customer', CustomerController::class)->names('manager.customer');
    Route::post('/importexcel', [CustomerController::class, 'import'])->name('manager.importexcel');
    //customer end
    //accessories
    Route::resource('/access', AccesoriesController::class)->names('manager.acces');
    Route::get('edit/access', [AccesoriesController::class, 'editMultiple'])->name('manager.acces.editmultiple');
    Route::post('update/access', [AccesoriesController::class, 'updateMultiple'])->name('manager.acces.updatemultiple');
    Route::post('check/access', [AccesoriesController::class, 'checkCode'])->name('manager.acces.checkcode');
    Route::get('/acces/{acces}/download', [AccesoriesController::class, 'download'])->name('acces.download');
    Route::get('acces-report', [AccesoriesController::class, 'report'])->name('manager.acces.report');
    //accessories end

    //report
    Route::get('report', [ReportController::class, 'index'])->name('manager.report.index');
    Route::get('/report/filter', [ReportController::class, 'filter'])->name('manager.report.filter');
    //report end
});
