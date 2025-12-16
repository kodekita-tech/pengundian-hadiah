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

// Participant Routes
use App\Http\Controllers\Admin\ParticipantController;
Route::get('/event/{event}/participants', [ParticipantController::class, 'index'])->name('event.participants.index');
Route::post('/event/{event}/participants/data', [ParticipantController::class, 'getData'])->name('event.participants.data');
Route::get('/event/{event}/participants/export', [ParticipantController::class, 'export'])->name('event.participants.export');
Route::post('/event/{event}/participants/import', [ParticipantController::class, 'import'])->name('event.participants.import');
Route::get('/event/participants/template', [ParticipantController::class, 'downloadTemplate'])->name('event.participants.template');
Route::delete('/event/participants/{id}', [ParticipantController::class, 'destroy'])->name('event.participants.destroy');
Route::delete('/event/{event}/participants/clear', [ParticipantController::class, 'clear'])->name('event.participants.clear');

