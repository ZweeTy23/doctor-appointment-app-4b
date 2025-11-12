@extends('admin.layouts.app')

@section('title', 'Editar usuario')

@section('content')
    <h1 class="h3 mb-3">Editar usuario</h1>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name"
                   value="{{ old('name', $user->name) }}"
                   class="form-control @error('name') is-invalid @enderror">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="email"
                   value="{{ old('email', $user->email) }}"
                   class="form-control @error('email') is-invalid @enderror">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <p class="text-muted">
            Si no deseas cambiar la contraseña, deja los siguientes campos vacíos.
        </p>

        <div class="mb-3">
            <label class="form-label">Nueva contraseña (opcional)</label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar nueva contraseña</label>
            <input type="password" name="password_confirmation"
                   class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection
