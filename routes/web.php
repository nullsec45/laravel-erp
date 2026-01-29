<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to Filament admin panel
Route::redirect('/', '/admin');

// Redirect old dashboard to Filament
Route::redirect('/dashboard', '/admin')->middleware(['auth', 'verified']);

// Redirect profile to Filament (handled by Filament's user menu)
Route::redirect('/profile', '/admin')->middleware(['auth']);
