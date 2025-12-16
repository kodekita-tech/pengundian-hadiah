<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OpdController;

Route::get('/opd', [OpdController::class, 'index'])->name('opd.index');
Route::post('/opd/data', [OpdController::class, 'getData'])->name('opd.data');
Route::post('/opd', [OpdController::class, 'store'])->name('opd.store');
Route::get('/opd/{id}', [OpdController::class, 'show'])->name('opd.show');
Route::put('/opd/{id}', [OpdController::class, 'update'])->name('opd.update');
Route::delete('/opd/{id}', [OpdController::class, 'destroy'])->name('opd.destroy');
Route::get('/opd/template/download', [OpdController::class, 'downloadTemplate'])->name('opd.downloadTemplate');
Route::post('/opd/import', [OpdController::class, 'import'])->name('opd.import');

