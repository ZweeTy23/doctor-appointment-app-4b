<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Speciality;

class DoctorController extends Controller
{
    public function index()
    {
        return view('admin.doctors.index');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'speciality']);
        return view('admin.doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load(['user', 'speciality']);
        $specialities = Speciality::all();
        return view('admin.doctors.edit', compact('doctor', 'specialities'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'speciality_id'          => 'nullable|exists:specialities,id',
            'medical_license_number' => 'nullable|string|max:255|unique:doctors,medical_license_number,' . $doctor->id,
            'biography'              => 'nullable|string',
        ]);

        $doctor->update([
            'speciality_id'          => $request->speciality_id ?: null,
            'medical_license_number' => $request->medical_license_number ?: null,
            'biography'              => $request->biography ?: null,
        ]);

        return redirect()->route('admin.doctors.index')
            ->with('swal', ['icon' => 'success', 'title' => 'Doctor actualizado']);
    }
}
