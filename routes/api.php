<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('notifications')
    ->name('api.notifications.')
    ->group(function () {

    Route::post('/', [App\Http\Controllers\NotificationController::class, 'store'])
        ->name('store');

})->middleware(['auth:sanctum', 'cache.control:300']);
