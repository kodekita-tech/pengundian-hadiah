<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guest\EventController;
use App\Http\Controllers\Guest\DrawController;
use App\Http\Controllers\Guest\QrController;

/*
|--------------------------------------------------------------------------
| Guest Event Routes
|--------------------------------------------------------------------------
|
| Routes untuk halaman event (public, tidak perlu login)
|
*/

Route::get('/event', [EventController::class, 'index'])->name('event.index');
Route::get('/d/{shortlink}', [DrawController::class, 'show'])->name('draw.show');
Route::post('/d/{shortlink}/verify', [DrawController::class, 'verifyPasskey'])->name('draw.verify');

// API Routes for Draw Page (Guest)
Route::get('/d/{shortlink}/candidates', [DrawController::class, 'getCandidates'])->name('draw.candidates');
Route::get('/d/{shortlink}/coupon-numbers', [DrawController::class, 'getAllCouponNumbers'])->name('draw.coupon-numbers');
Route::post('/d/{shortlink}/winner', [DrawController::class, 'storeWinner'])->name('draw.winner');
Route::post('/d/{shortlink}/winners', [DrawController::class, 'storeWinners'])->name('draw.winners');

// QR Registration Routes
Route::get('/qr/{token}', [QrController::class, 'show'])->name('qr.show');
Route::get('/qr/{token}/refresh-captcha', [QrController::class, 'refreshCaptcha'])->name('qr.refresh-captcha');
Route::post('/qr/{token}/register', [QrController::class, 'register'])->name('qr.register');

