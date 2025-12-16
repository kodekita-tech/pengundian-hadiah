<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| Guest Event Routes
|--------------------------------------------------------------------------
|
| Routes untuk halaman event (public, tidak perlu login)
|
*/

Route::get('/event', [EventController::class, 'index'])->name('event.index');

