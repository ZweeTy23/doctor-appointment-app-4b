<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // CORRECCIÓN 1: Obtener los roles y pasarlos a la vista
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // CORRECCIÓN 2: Validar los nuevos campos y el rol
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'id_number' => 'required|string|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:users', // Ajusta la longitud según necesites
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string|max:255',
            'role_id'   => 'required|exists:roles,id', // Validar que el rol exista
        ]);

        // Crear el usuario con TODOS los campos
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => $request->password, // Laravel lo hashea automáticamente por el cast en el modelo
            'id_number' => $request->id_number,
            'phone'     => $request->phone,
            'address'   => $request->address,
        ]);

        // Asignar el rol usando Spatie
        $user->roles()->sync($request->role_id);

        // Si el rol asignado es "Paciente", crear automáticamente el expediente
        $role = Role::find($request->role_id);
        if ($role && $role->name === 'Paciente') {
            Patient::create([
                'user_id' => $user->id,
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Usuario creado correctamente',
                'text'  => 'El usuario ha sido creado y el rol asignado exitosamente',
            ]);
    }
#comentario para que pueda hacer commit
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // También necesitarás pasar los roles aquí para el select de edición
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Validar también los nuevos campos en la edición
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:8|confirmed',
            'id_number' => 'required|string|max:20',
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string|max:255',
            'role_id'   => 'required|exists:roles,id',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'id_number' => $request->id_number,
            'phone'     => $request->phone,
            'address'   => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        // Obtener el rol anterior y el nuevo
        $oldRole = $user->roles->first();
        $newRole = Role::find($request->role_id);

        // Actualizar el rol
        $user->roles()->sync($request->role_id);

        // Si el nuevo rol es "Paciente" y no tiene expediente, crearlo
        if ($newRole && $newRole->name === 'Paciente' && !$user->patient) {
            Patient::create([
                'user_id' => $user->id,
            ]);
        }

        // Si el rol anterior era "Paciente" y el nuevo no lo es, eliminar el expediente
        if ($oldRole && $oldRole->name === 'Paciente' && $newRole && $newRole->name !== 'Paciente') {
            $user->patient?->delete();
        }

        return redirect()
            ->route('admin.users.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Usuario actualizado correctamente',
                'text'  => 'El usuario ha sido actualizado exitosamente.',
            ]);
    }

    public function destroy(User $user)
    {
        // PROTECCIÓN: No permitir que el usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            // OPCIÓN PARA EL TEST: Si quieres que el test pase con assertStatus(403)
            abort(403, 'No puedes eliminar tu propia cuenta.');

            /* // Si prefieres redireccionar, el test debe esperar assertStatus(302)
            return redirect()
                ->route('admin.users.index') // <--- ELIMINADO EL ";" DE AQUÍ
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Acción no permitida',
                    'text'  => 'No puedes eliminar tu propia cuenta de administrador.',
                ]);
            */
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Usuario eliminado',
                'text'  => 'El usuario ha sido movido a la papelera (Soft Delete).',
            ]);
    }}
