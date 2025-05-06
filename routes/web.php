<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CounterController;

// Customer Routes
Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
Route::post('/take-number', [CustomerController::class, 'takeNumber'])->name('customer.takeNumber');

// Counter Management Routes
Route::get('/counter', [CounterController::class, 'index'])->name('counter.index');
Route::post('/counter/{id}/toggle-status', [CounterController::class, 'toggleStatus'])->name('counter.toggleStatus');
Route::post('/counter/{id}/complete-current', [CounterController::class, 'completeCurrent'])->name('counter.completeCurrent');
Route::post('/counter/{id}/call-next', [CounterController::class, 'callNext'])->name('counter.callNext');
