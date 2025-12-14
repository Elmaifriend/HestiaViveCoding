<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Invitations
    Route::apiResource('invitations', \App\Http\Controllers\Api\InvitationController::class);
    Route::post('invitations/validate', [\App\Http\Controllers\Api\InvitationController::class, 'validateQr']);
    
    // Payments
    Route::apiResource('payments', \App\Http\Controllers\Api\PaymentController::class);

    // Announcements
    Route::get('announcements', [\App\Http\Controllers\Api\AnnouncementController::class, 'index']);
    Route::get('announcements/{announcement}', [\App\Http\Controllers\Api\AnnouncementController::class, 'show']);

    // Amenities & Reservations
    Route::get('amenities', [\App\Http\Controllers\Api\AmenityController::class, 'index']);
    Route::apiResource('reservations', \App\Http\Controllers\Api\ReservationController::class)->only(['index', 'store']);

    // Incidents
    Route::apiResource('incidents', \App\Http\Controllers\Api\IncidentController::class);

    // Chat
    Route::get('chat', [\App\Http\Controllers\Api\ChatController::class, 'index']);
    Route::post('chat', [\App\Http\Controllers\Api\ChatController::class, 'store']);

    // Gate Access
    Route::get('gate-access', [\App\Http\Controllers\Api\GateAccessController::class, 'index']);
    Route::post('gate-access', [\App\Http\Controllers\Api\GateAccessController::class, 'store']);

    // Marketplace
    Route::apiResource('marketplace', \App\Http\Controllers\Api\MarketplaceController::class)->only(['index', 'store', 'show']);
});
