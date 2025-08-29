<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('/users', App\Http\Controllers\API\UserController::class);


Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
});
Route::prefix('auth')->name('auth.')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
});
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json($request->user());
});
Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('/addresses', App\Http\Controllers\API\AddressController::class);

    Route::apiResource('/categories', App\Http\Controllers\API\CategoryController::class);

    Route::apiResource('/venues', App\Http\Controllers\API\VenueController::class);

    Route::apiResource('/spaces', App\Http\Controllers\API\SpaceController::class);

    Route::apiResource('/activities', App\Http\Controllers\API\ActivityController::class);

    Route::apiResource('/time_slots', App\Http\Controllers\API\TimeSlotController::class);

    Route::apiResource('/amenities', App\Http\Controllers\API\AmenityController::class);

    Route::apiResource('/properties', App\Http\Controllers\API\PropertyController::class);

    Route::apiResource('/rooms', App\Http\Controllers\API\RoomController::class);

    Route::apiResource('/bookings', App\Http\Controllers\API\BookingController::class);

    Route::apiResource('/wallets', App\Http\Controllers\API\WalletController::class);

    Route::apiResource('/withdrawals', App\Http\Controllers\API\WithdrawalController::class);

    Route::apiResource('/menu_items', App\Http\Controllers\API\MenuItemController::class);

    Route::apiResource('/menus', App\Http\Controllers\API\MenuController::class);

    Route::apiResource('/wallet_transactions', App\Http\Controllers\API\WalletTransactionController::class);

    Route::apiResource('/commissions', App\Http\Controllers\API\CommissionController::class);


Route::apiResource('/venue_opening_hours', App\Http\Controllers\API\VenueOpeningHourController::class);
});


Route::apiResource('/images', App\Http\Controllers\API\ImageController::class);
