<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ConsultationController;
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

// Gestión de doctores (solo ver y editar, se crean desde usuarios)
Route::resource('doctors', DoctorController::class)
    ->only(['index', 'show', 'edit', 'update'])
    ->names('doctors');

// ─────────────────────────────────────────────────────────────
// Calendario — Vista mensual tipo Outlook de la disponibilidad
// de cada doctor, con colores por estado (disponible, parcial,
// ocupado, no disponible). Permite ver detalle por día.
// ─────────────────────────────────────────────────────────────
Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');

// ─────────────────────────────────────────────────────────────
// Módulo de Horarios — Gestor de disponibilidad de doctores
// Permite configurar los bloques de 1 hora en los que
// cada doctor está disponible para recibir citas.
// ─────────────────────────────────────────────────────────────
Route::get('schedules/{doctor}', [ScheduleController::class, 'index'])->name('schedules.index');

// ─────────────────────────────────────────────────────────────
// Módulo de Citas Médicas — Programar, editar y cancelar citas
// Incluye búsqueda de disponibilidad, validación de conflictos
// (overlap) y verificación de horarios del doctor.
// CRUD completo: listar, crear, editar y eliminar.
// ─────────────────────────────────────────────────────────────
Route::resource('appointments', AppointmentController::class)
    ->except(['show'])
    ->names('appointments');

Route::get('appointments/{appointment}/consultation', [ConsultationController::class, 'show'])->name('admin.consultations.show');

// ─────────────────────────────────────────────────────────────
// Módulo de Soporte — Tickets de soporte técnico
// Permite a los usuarios reportar problemas y al admin gestionar
// el estado, prioridad y respuesta de cada ticket.
// CRUD completo: listar, crear, ver detalle, editar y eliminar.
// ─────────────────────────────────────────────────────────────
Route::resource('tickets', SupportTicketController::class)->names('tickets');

