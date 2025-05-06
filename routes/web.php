<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketSystemController;

Route::get('/', [TicketSystemController::class, 'index'])->name('home');
Route::post('/take-number', [TicketSystemController::class, 'takeNumber'])->name('takeNumber');
Route::post('/toggle-status/{id}', [TicketSystemController::class, 'toggleStatus'])->name('toggleStatus');
Route::post('/complete-current/{id}', [TicketSystemController::class, 'completeCurrent'])->name('completeCurrent');
Route::post('/call-next/{id}', [TicketSystemController::class, 'callNext'])->name('callNext');
