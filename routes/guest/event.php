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
Route::get('/d/{shortlink}', [\App\Http\Controllers\DrawController::class, 'show'])->name('draw.show');
Route::post('/d/{shortlink}/verify', [\App\Http\Controllers\DrawController::class, 'verifyPasskey'])->name('draw.verify');

// API Routes for Draw Page (Guest)
Route::get('/d/{shortlink}/candidates', [\App\Http\Controllers\DrawController::class, 'getCandidates'])->name('draw.candidates');
Route::post('/d/{shortlink}/winner', [\App\Http\Controllers\DrawController::class, 'storeWinner'])->name('draw.winner');

// QR Registration Routes
Route::get('/qr/{token}', [\App\Http\Controllers\QrController::class, 'show'])->name('qr.show');
Route::post('/qr/{token}/register', [\App\Http\Controllers\QrController::class, 'register'])->name('qr.register');

