<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function (){
    return view('admin.dashboard');
})->name('dashboard');

//Gestion de roles
Route::resource('roles', RoleController::class)->names('admin.roles');

//Gestion de usuarios
Route::resource('users', UserController::class)->names('admin.users');

//Gestion de pacientes (solo ver y editar, se crean desde usuarios)
Route::resource('patients', PatientController::class)
    ->only(['index', 'show', 'edit', 'update'])
    ->names('admin.patients');
