<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    /**
     * Mostrar el calendario tipo Outlook con disponibilidad de doctores.
     */
    public function index()
    {
        return view('admin.calendar.index');
    }
}
