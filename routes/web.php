<?php

use App\Http\Controllers\ApiKeyManagementController;
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
    return view('welcome');
});

Route::middleware(['auth.domeggook'])->group(function () {
    Route::get('/key/index', [ApiKeyManagementController::class, 'index'])->name('key.index');
    Route::post('/key/store', [ApiKeyManagementController::class, 'store'])->name('key.store');
    Route::post('/key/edit', [ApiKeyManagementController::class, 'edit'])->name('key.edit');
    Route::post('/key/update', [ApiKeyManagementController::class, 'update'])->name('key.update');
    Route::post('/key/delete', [ApiKeyManagementController::class, 'destroy'])->name('key.destroy');
});

require __DIR__.'/auth.php';
