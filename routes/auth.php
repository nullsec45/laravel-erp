<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Authentication is now handled by Filament.
| All auth routes redirect to Filament admin panel.
|
*/

// Redirect old auth routes to Filament
Route::middleware('guest')->group(function () {
    Route::redirect('/register', '/admin/login');
    Route::redirect('/login', '/admin/login');
    Route::redirect('/forgot-password', '/admin/login');
    Route::redirect('/reset-password/{token}', '/admin/login');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/verify-email', '/admin');
    Route::redirect('/confirm-password', '/admin');
});
