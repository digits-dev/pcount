<?php

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


Route::get('admin/count_headers/scan', [AdminCountHeadersController::class, 'getScan'])->name('count.scan');
Route::post('admin/count_headers/save', [AdminCountHeadersController::class, 'saveScan'])->name('count.save-scan');
Route::post('admin/count_headers/export', [AdminCountHeadersController::class, 'countExport'])->name('count.export');

Route::post('admin/get-category-tags', [AdminUserCategoryTagsController::class, 'getCategoryTagByCategory'])->name('count.get-category-tags');
Route::post('admin/set-used-category-tags', [AdminUserCategoryTagsController::class, 'setUsedCategoryTag'])->name('count.set-used-category-tags');
Route::post('admin/get-item', [AdminItemsController::class, 'getItem'])->name('count.get-item');
Route::post('admin/get-passcode', [AdminCountTypesController::class, 'getPassCode'])->name('count.get-passcode');

Route::get('admin/user_category_tags/get-import', [AdminUserCategoryTagsController::class, 'getImport'])->name('count-tags.get-import');
Route::get('admin/user_category_tags/get-template', [AdminUserCategoryTagsController::class, 'getTemplate'])->name('count-tags.get-template');
Route::post('admin/user_category_tags/import', [AdminUserCategoryTagsController::class, 'importCountTags'])->name('count-tags.import');

Route::post('admin/save-temp-header', [CountTempHeaderController::class, 'saveCountHeaders'])->name('count.save-temp-header');
Route::post('admin/get-temp-header', [CountTempHeaderController::class, 'getCountHeaders'])->name('count.get-temp-header');
Route::post('admin/save-temp-lines', [CountTempLineController::class, 'saveCountLines'])->name('count.save-temp-line');
Route::post('admin/update-temp-lines', [CountTempLineController::class, 'updateItemQty'])->name('count.update-temp-line');
Route::post('admin/update-temp-revised-lines', [CountTempLineController::class, 'updateItemRevisedQty'])->name('count.update-temp-line-revised');

