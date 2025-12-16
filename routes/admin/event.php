<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EventController;

Route::get('/event', [EventController::class, 'index'])->name('event.index');
Route::get('/event/create', [EventController::class, 'create'])->name('event.create');
Route::post('/event', [EventController::class, 'store'])->name('event.store');
Route::get('/event/{event}', [EventController::class, 'show'])->name('event.show');
Route::get('/event/{event}/edit', [EventController::class, 'edit'])->name('event.edit');
Route::put('/event/{event}', [EventController::class, 'update'])->name('event.update');
Route::delete('/event/{event}', [EventController::class, 'destroy'])->name('event.destroy');
Route::post('/event/{event}/update-status', [EventController::class, 'updateStatus'])->name('event.update-status');
Route::post('/event/{event}/regenerate-qr', [EventController::class, 'regenerateQrToken'])->name('event.regenerate-qr');
Route::get('/event/opd/data', [EventController::class, 'getOpdData'])->name('event.opd.data');

