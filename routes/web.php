<?php

use App\Http\Controllers\AdminCmsUsersController;
use App\Http\Controllers\AdminCountHeadersController;
use App\Http\Controllers\AdminCountTypesController;
use App\Http\Controllers\AdminItemsController;
use App\Http\Controllers\AdminUserCategoryTagsController;
use App\Http\Controllers\CountTempHeaderController;
use App\Http\Controllers\CountTempLineController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::group(['middleware' => ['web'], 'prefix' => config('crudbooster.ADMIN_PATH'), 'namespace' => 'App\Http\Controllers'], function(){
    // count
    Route::group(['prefix' => 'count_headers'], function () {
        Route::get('print/{id}', [AdminCountHeadersController::class, 'getPrint'])->name('count.print');
        Route::get('scan', [AdminCountHeadersController::class, 'getScan'])->name('count.scan');
        Route::post('save', [AdminCountHeadersController::class, 'saveScan'])->name('count.save-scan');
        Route::post('export', [AdminCountHeadersController::class, 'countExport'])->name('count.export');
    });

    //user category tags
    Route::group(['prefix' => 'user_category_tags'], function () {
        Route::get('get-import', [AdminUserCategoryTagsController::class, 'getImport'])->name('count-tags.get-import');
        Route::get('get-template', [AdminUserCategoryTagsController::class, 'getTemplate'])->name('count-tags.get-template');
        Route::post('import', [AdminUserCategoryTagsController::class, 'importCountTags'])->name('count-tags.import');
        Route::post('get-category-tags', [AdminUserCategoryTagsController::class, 'getCategoryTagByCategory'])->name('count.get-category-tags');
        Route::post('set-used-category-tags', [AdminUserCategoryTagsController::class, 'setUsedCategoryTag'])->name('count.set-used-category-tags');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('get-import', [AdminCmsUsersController::class, 'getImport'])->name('users.get-import');
        Route::get('get-template', [AdminCmsUsersController::class, 'getTemplate'])->name('users.get-template');
        Route::post('import', [AdminCmsUsersController::class, 'importUsers'])->name('users.import');
    });

    Route::group(['prefix' => 'items'], function () {
        Route::post('get-item', [AdminItemsController::class, 'getItem'])->name('count.get-item');
        Route::get('get-new-item', [AdminItemsController::class, 'getNewItem'])->name('items.pull-new-item');
        Route::get('update-item', [AdminItemsController::class, 'getUpdateItem'])->name('items.pull-update-item');
    });

    Route::group(['prefix' => 'temps'], function () {
        Route::post('save-temp-header', [CountTempHeaderController::class, 'saveCountHeaders'])->name('count.save-temp-header');
        Route::post('get-temp-header', [CountTempHeaderController::class, 'getCountHeaders'])->name('count.get-temp-header');
        Route::post('save-temp-lines', [CountTempLineController::class, 'saveCountLines'])->name('count.save-temp-line');
        Route::post('update-temp-lines', [CountTempLineController::class, 'updateItemQty'])->name('count.update-temp-line');
        Route::post('update-temp-revised-lines', [CountTempLineController::class, 'updateItemRevisedQty'])->name('count.update-temp-line-revised');
    });

    Route::post('get-passcode', [AdminCountTypesController::class, 'getPassCode'])->name('count.get-passcode');

});
