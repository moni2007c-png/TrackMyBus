<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/routes', function () {
    return view('routes');
})->name('routes');


/*
|--------------------------------------------------------------------------
| Bus API Routes
|--------------------------------------------------------------------------
*/

// Public bus information
Route::get('/api/buses', [BusController::class, 'index']);
Route::get('/api/buses/{bus_number}', [BusController::class, 'show']);

// Admin-only bus management
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::post('/api/buses', [BusController::class, 'store']);

    Route::put('/api/buses/{bus_number}', [BusController::class, 'update']);

    Route::delete('/api/buses/{bus_number}', [BusController::class, 'destroy']);

    Route::put(
        '/api/buses/{bus_number}/assign-driver',
        [BusController::class, 'assignDriver']
    );
});

// Driver-only location updates
Route::put(
    '/api/buses/{bus_number}/location',
    [BusController::class, 'updateLocation']
)->middleware(['auth', 'verified', 'role:driver']);


/*
|--------------------------------------------------------------------------
| Passenger, Driver and Admin Pages
|--------------------------------------------------------------------------
*/

// Passenger tracking page
Route::get('/tracking', function () {
    return view('tracking');
})->middleware(['auth', 'verified', 'role:passenger']);

// Driver dashboard
Route::get('/driver', function () {
    return view('driver');
})->middleware(['auth', 'verified', 'role:driver']);

// Admin dashboard
Route::get('/admin', function () {
    return view('admin');
})->middleware(['auth', 'verified', 'role:admin']);


/*
|--------------------------------------------------------------------------
| Role-Based Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect('/admin'),
        'driver' => redirect('/driver'),
        'passenger' => redirect('/tracking'),
        default => redirect('/'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');
require __DIR__ . '/settings.php';