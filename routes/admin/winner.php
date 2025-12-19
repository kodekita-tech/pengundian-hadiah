<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\WinnerController;

/*
|--------------------------------------------------------------------------
| Admin Winner Routes
|--------------------------------------------------------------------------
|
| Routes untuk management pemenang
|
*/

Route::get('/winner', [WinnerController::class, 'index'])->name('winner.index');
Route::delete('/winner/{id}', [WinnerController::class, 'destroy'])->name('winner.destroy');

