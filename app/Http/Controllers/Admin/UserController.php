<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role; // <--- IMPORTANTE: Importar el modelo Role

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

        // CORRECCIÓN 3: Asignar el rol usando Spatie
        // No se guarda en la tabla 'users', sino en la tabla pivote de Spatie
        $user->roles()->sync($request->role_id);

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

        // Actualizar el rol
        $user->roles()->sync($request->role_id);

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
            return redirect()
                ->route('admin.users.index')
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Acción no permitida',
                    'text'  => 'No puedes eliminar tu propia cuenta de administrador.',
                ]);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Usuario eliminado',
                'text'  => 'El usuario ha sido movido a la papelera (Soft Delete).',
            ]);
    }
}
