<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;

class ScheduleController extends Controller
{
    /**
     * Mostrar el gestor de horarios de un doctor.
     */
    public function index(Doctor $doctor)
    {
        $doctor->load('user');
        return view('admin.schedules.index', compact('doctor'));
    }
}
