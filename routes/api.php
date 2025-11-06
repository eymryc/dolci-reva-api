<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// =============================================================================
// ROUTES PUBLIQUES (Sans authentification)
// =============================================================================

// Authentification
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
    Route::post('/register', [App\Http\Controllers\API\UserController::class, 'store']);
});

// Vérification d'email (routes publiques)
Route::prefix('email')->name('email.')->group(function () {
    Route::get('/verify/{id}/{hash}', [App\Http\Controllers\API\AuthController::class, 'verifyEmail'])
        ->name('verification.verify'); // Le nom complet sera: email.verification.verify
    Route::post('/verification-notification', [App\Http\Controllers\API\AuthController::class, 'resendVerificationEmail'])
        ->name('verification.send'); // Le nom complet sera: email.verification.send
});

// Ressources publiques
Route::apiResource('/business-types', App\Http\Controllers\API\BusinessTypeController::class);

// =============================================================================
// ROUTES PROTÉGÉES (Avec authentification)
// =============================================================================



// Route non authentifiées
Route::prefix('public')->name('public.')->group(function () {


    // -------------------------------------------------------------------------
    // OPINIONS
    // -------------------------------------------------------------------------
    Route::get('/opinions/{id}', [App\Http\Controllers\API\OpinionController::class, 'getOpinionById']);

    // -------------------------------------------------------------------------
    // Hotels
    // -------------------------------------------------------------------------
    Route::get('/hotels', [App\Http\Controllers\API\HotelController::class, 'getAllHotels']);


    // -------------------------------------------------------------------------
    // RESIDENCES
    // -------------------------------------------------------------------------
    Route::get('/residences', [App\Http\Controllers\API\ResidenceController::class, 'getAllResidences']);
    Route::get('/residences/{residence}', [App\Http\Controllers\API\ResidenceController::class, 'getResidence']);
    
    // -------------------------------------------------------------------------
    // RESTAURANTS
    // -------------------------------------------------------------------------
    Route::get('/restaurants', [App\Http\Controllers\API\RestaurantController::class, 'getAllRestaurants']);
    
    // -------------------------------------------------------------------------
    // LOUNGES
    // -------------------------------------------------------------------------
    Route::get('/lounges', [App\Http\Controllers\API\LoungeController::class, 'getAllLounges']);
    
    // -------------------------------------------------------------------------
    // NIGHT CLUBS
    // -------------------------------------------------------------------------
    Route::get('/night-clubs', [App\Http\Controllers\API\NightClubController::class, 'getAllNightClubs']);
    
});


// Route authentifiées
Route::middleware(['auth:sanctum'])->group(function () {
    
    // -------------------------------------------------------------------------
    // AUTHENTIFICATION & UTILISATEURS
    // -------------------------------------------------------------------------
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
    });


    // -------------------------------------------------------------------------
    // Profil utilisateur
    // -------------------------------------------------------------------------
    // Route::apiResource('/users', App\Http\Controllers\API\UserController::class);
    Route::get('/profile', [App\Http\Controllers\API\UserController::class, 'getProfile']);
    Route::put('/profile', [App\Http\Controllers\API\UserController::class, 'updateProfile']);




    // Route opinions
    // -------------------------------------------------------------------------
    Route::apiResource('/opinions', App\Http\Controllers\API\OpinionController::class);



    // -------------------------------------------------------------------------
    // Users
    // -------------------------------------------------------------------------
    Route::apiResource('/users', App\Http\Controllers\API\UserController::class);

    // -------------------------------------------------------------------------
    // HÉBERGEMENT - RÉSIDENCES
    // -------------------------------------------------------------------------
    Route::apiResource('/residences', App\Http\Controllers\API\ResidenceController::class);
    Route::get('/residences/{residence}/availability', [App\Http\Controllers\API\ResidenceController::class, 'checkAvailability']);

    // -------------------------------------------------------------------------
    // HÉBERGEMENT - HÔTELS & CHAMBRES
    // -------------------------------------------------------------------------
    Route::apiResource('/hotels', App\Http\Controllers\API\HotelController::class);
    Route::apiResource('hotels.rooms', App\Http\Controllers\API\HotelRoomController::class);
    Route::get('/hotels/{hotel}/rooms', [App\Http\Controllers\API\HotelRoomController::class, 'getByHotel']);

    // -------------------------------------------------------------------------
    // RESTAURANTS & RÉSERVATIONS
    // -------------------------------------------------------------------------
    Route::apiResource('/restaurants', App\Http\Controllers\API\RestaurantController::class);
    Route::get('/restaurants/{restaurant}/available-tables', [App\Http\Controllers\API\RestaurantController::class, 'getAvailableTables']);
    Route::get('/restaurants/{restaurant}/time-slots', [App\Http\Controllers\API\RestaurantController::class, 'getAvailableTimeSlots']);

    // -------------------------------------------------------------------------
    // LOUNGES & RÉSERVATIONS
    // -------------------------------------------------------------------------
    Route::apiResource('/lounges', App\Http\Controllers\API\LoungeController::class);
    Route::get('/lounges/{lounge}/available-tables', [App\Http\Controllers\API\LoungeController::class, 'getAvailableTables']);
    Route::get('/lounges/{lounge}/recommended-tables', [App\Http\Controllers\API\LoungeController::class, 'getRecommendedTables']);
    Route::get('/lounges/{lounge}/time-slots', [App\Http\Controllers\API\LoungeController::class, 'getAvailableTimeSlots']);

    // -------------------------------------------------------------------------
    // NIGHT CLUBS & RÉSERVATIONS
    // -------------------------------------------------------------------------
    Route::apiResource('/night-clubs', App\Http\Controllers\API\NightClubController::class);
    Route::get('/night-clubs/{nightClub}/available-areas', [App\Http\Controllers\API\NightClubController::class, 'getAvailableAreas']);
    Route::get('/night-clubs/{nightClub}/recommended-areas', [App\Http\Controllers\API\NightClubController::class, 'getRecommendedAreas']);
    Route::get('/night-clubs/{nightClub}/time-slots', [App\Http\Controllers\API\NightClubController::class, 'getAvailableTimeSlots']);
        Route::get('/night-clubs/age/{ageRestriction}', [App\Http\Controllers\API\NightClubController::class, 'getByAgeRestriction']);

    // -------------------------------------------------------------------------
    // RÉSERVATIONS
    // -------------------------------------------------------------------------
    Route::apiResource('/bookings', App\Http\Controllers\API\BookingController::class);
    
    // Réservations spécifiques
    Route::post('/residences/{residence}/book', [App\Http\Controllers\API\BookingController::class, 'bookResidence']);
    Route::post('/hotels/{hotel}/book', [App\Http\Controllers\API\BookingController::class, 'bookHotel']);
    Route::post('/restaurants/{restaurant}/book', [App\Http\Controllers\API\BookingController::class, 'bookRestaurant']);
    Route::post('/lounges/{lounge}/book', [App\Http\Controllers\API\BookingController::class, 'bookLounge']);
    Route::post('/night-clubs/{nightClub}/book', [App\Http\Controllers\API\BookingController::class, 'bookNightClub']);
    
    // Gestion des réservations
    Route::patch('/bookings/{booking}/confirm', [App\Http\Controllers\API\BookingController::class, 'confirmBooking']);
    Route::patch('/bookings/{booking}/cancel', [App\Http\Controllers\API\BookingController::class, 'cancelBooking']);
    Route::patch('/bookings/{booking}/complete', [App\Http\Controllers\API\BookingController::class, 'completeBooking']);

    // -------------------------------------------------------------------------
    // ÉQUIPEMENTS & SERVICES
    // -------------------------------------------------------------------------
    Route::apiResource('/amenities', App\Http\Controllers\API\AmenityController::class);

    // -------------------------------------------------------------------------
    // GESTION DES MÉDIAS
    // -------------------------------------------------------------------------
    Route::prefix('media')->name('media.')->group(function () {
        Route::post('/upload', [App\Http\Controllers\API\MediaController::class, 'upload']);
        Route::get('/get', [App\Http\Controllers\API\MediaController::class, 'getMedia']);
        Route::delete('/{media}', [App\Http\Controllers\API\MediaController::class, 'deleteMedia']);
        Route::delete('/clear-collection', [App\Http\Controllers\API\MediaController::class, 'clearCollection']);
    });


    // -------------------------------------------------------------------------
    // FINANCE & PAIEMENTS
    // -------------------------------------------------------------------------
    Route::apiResource('/wallets', App\Http\Controllers\API\WalletController::class);
    Route::apiResource('/wallet_transactions', App\Http\Controllers\API\WalletTransactionController::class);
    Route::apiResource('/withdrawals', App\Http\Controllers\API\WithdrawalController::class);
    Route::apiResource('/commissions', App\Http\Controllers\API\CommissionController::class);

    // Paystack Payment Routes
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::post('/initialize', [App\Http\Controllers\API\PaymentController::class, 'initialize']);
        Route::post('/verify', [App\Http\Controllers\API\PaymentController::class, 'verify']);
        Route::post('/webhook', [App\Http\Controllers\API\PaymentController::class, 'webhook']);
    });
});


