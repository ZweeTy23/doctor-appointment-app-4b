<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Comprobante de cita') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 16px; border-bottom: 2px solid #2563eb; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #e5e7eb; }
        th { width: 32%; color: #4b5563; font-weight: normal; }
        .muted { color: #6b7280; font-size: 10px; margin-top: 24px; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }} — {{ __('Comprobante de cita médica') }}</h1>
    <p><strong>{{ __('Folio') }}:</strong> #{{ $appointment->id }}</p>

    <table>
        <tr>
            <th>{{ __('Paciente') }}</th>
            <td>{{ $appointment->patient->user->name ?? '—' }}</td>
        </tr>
        <tr>
            <th>{{ __('Doctor') }}</th>
            <td>{{ $appointment->doctor->user->name ?? '—' }}</td>
        </tr>
        @if($appointment->doctor->speciality)
            <tr>
                <th>{{ __('Especialidad') }}</th>
                <td>{{ $appointment->doctor->speciality->name }}</td>
            </tr>
        @endif
        <tr>
            <th>{{ __('Fecha') }}</th>
            <td>{{ $appointment->date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>{{ __('Horario') }}</th>
            <td>
                {{ \Illuminate\Support\Str::substr($appointment->start_time, 0, 5) }}
                —
                {{ \Illuminate\Support\Str::substr($appointment->end_time, 0, 5) }}
            </td>
        </tr>
        <tr>
            <th>{{ __('Estado') }}</th>
            <td>{{ ucfirst($appointment->status) }}</td>
        </tr>
        @if($appointment->reason)
            <tr>
                <th>{{ __('Motivo') }}</th>
                <td>{{ $appointment->reason }}</td>
            </tr>
        @endif
    </table>

    <p class="muted">{{ __('Documento generado automáticamente.') }}</p>
</body>
</html>
