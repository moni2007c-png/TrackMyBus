<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/routes', function () {
    return view('routes');
})->name('routes');

// Bus API routes
Route::get('/api/buses', [BusController::class, 'index']);
Route::get('/api/buses/{bus_number}', [BusController::class, 'show']);

Route::post('/api/buses', [BusController::class, 'store'])
    ->middleware(['auth','role:admin']);

Route::put('/api/buses/{bus_number}', [BusController::class, 'update'])
    ->middleware(['auth', 'role:admin']);

Route::delete('/api/buses/{bus_number}', [BusController::class, 'destroy'])
    ->middleware(['auth', 'role:admin']);
    
Route::put('/api/buses/{bus_number}/location', [BusController::class, 'updateLocation'])
    ->middleware(['auth', 'role:driver']);

Route::put('/api/buses/{bus_number}/assign-driver', [BusController::class, 'assignDriver'])
    ->middleware(['auth', 'role:admin']);

Route::get('/tracking', function () {
    return view('tracking');
})->middleware(['auth', 'role:passenger']);

Route::get('/driver', function () {
    return view('driver');
})->middleware(['auth', 'role:driver']);

Route::get('/admin', function () {
    return view('admin');
})->middleware(['auth','role:admin']);

Route::get('/dashboard', function () {

    return match (auth()->user()->role) {

        'admin' => redirect('/admin'),

        'driver' => redirect('/driver'),

        'passenger' => redirect('/tracking'),

        default => redirect('/'),

    };

})->middleware(['auth'])->name('dashboard');