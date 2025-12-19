<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('guest.landing');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Guest Routes (public, no auth required)
require __DIR__.'/guest/event.php';

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin/dashboard.php';
    require __DIR__.'/admin/users.php';
    require __DIR__.'/admin/opd.php';
    require __DIR__.'/admin/event.php';
    require __DIR__.'/admin/profile.php';
    require __DIR__.'/admin/winner.php';
});
