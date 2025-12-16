<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users/data', [UserController::class, 'getData'])->name('users.data');
Route::get('/users/roles', [UserController::class, 'getRoles'])->name('users.roles');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/users/template/download', [UserController::class, 'downloadTemplate'])->name('users.downloadTemplate');
Route::post('/users/import', [UserController::class, 'import'])->name('users.import');

