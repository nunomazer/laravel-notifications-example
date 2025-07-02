<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home')
    ->middleware('auto.auth');

Route::prefix('notifications')
    ->name('notifications.')
    ->middleware(['auto.auth'])
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])
            ->name('index');
    });

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auto.auth');