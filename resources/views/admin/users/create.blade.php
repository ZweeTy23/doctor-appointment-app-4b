@extends('admin.layouts.app')

@section('title', 'Crear usuario')

@section('content')
    <h1 class="h3 mb-3">Crear usuario</h1>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name"
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="password_confirmation"
                   class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection
