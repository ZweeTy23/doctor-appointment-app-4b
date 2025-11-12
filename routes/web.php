<?php

use Illuminate\Support\Facades\Route;

// File: routes/web.php
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ... otras rutas admin (roles, users, etc.)
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::prefix('admin')->name('admin.')->middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard de admin
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // CRUD de roles
    Route::resource('roles', RoleController::class);
});
