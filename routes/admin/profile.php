<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfileController;

Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

