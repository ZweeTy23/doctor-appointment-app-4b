<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use Illuminate\Support\Facades\Route;

// Dashboard admin
Route::get('/', function (){
    return view('admin.dashboard');
})->name('dashboard');

// Gestión de roles
Route::resource('roles', RoleController::class)->names('roles');

// Gestión de usuarios
Route::resource('users', UserController::class)->names('users');

// Gestión de pacientes (solo ver y editar, se crean desde usuarios)
Route::resource('patients', PatientController::class)
    ->only(['index', 'show', 'edit', 'update'])
    ->names('patients');
