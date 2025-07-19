<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoyaltyCardController;
use App\Http\Controllers\NotificationController;

// Loyalty Card Routes
Route::prefix('loyalty-cards')->group(function () {
    Route::post('/', [LoyaltyCardController::class, 'createCard']);
    Route::get('/', [LoyaltyCardController::class, 'listCards']);
    Route::get('/{cardId}', [LoyaltyCardController::class, 'getCard']);
    Route::patch('/{cardId}/points', [LoyaltyCardController::class, 'updatePoints']);
});

// Notification Routes
Route::prefix('notifications')->group(function () {
    Route::post('/push', [NotificationController::class, 'sendPushNotification']);
    Route::post('/geo', [NotificationController::class, 'sendGeoNotification']);
    Route::get('/{cardId}', [NotificationController::class, 'getNotifications']);
});