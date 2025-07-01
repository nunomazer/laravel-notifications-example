<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')
    ->name('api.notifications.')
    ->middleware(['auto.auth'])
    ->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])
            ->name('index');

        Route::post('/', [App\Http\Controllers\NotificationController::class, 'store'])
            ->name('store');

        Route::put('/{notification}/mark-read', [App\Http\Controllers\NotificationController::class, 'putMarkAsRead'])
            ->name('mark-read');
    });

Route::prefix('users')
    ->name('api.users.')
    ->middleware(['auto.auth'])
    ->group(function () {
        Route::get('/me', function (Request $request) {
            return $request->user();
        })->name('me');

        Route::get(
            '/{user}/notifications/latest',
            [App\Http\Controllers\NotificationController::class, 'getLatestByUser']
        )
            ->name('notifications.latest');
    });//->middleware(['auth:sanctum', 'cache.control:300']);