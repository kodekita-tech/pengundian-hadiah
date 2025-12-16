<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes
|--------------------------------------------------------------------------
|
| Routes untuk dashboard admin
|
*/

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

