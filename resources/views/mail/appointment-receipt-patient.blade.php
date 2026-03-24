<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.5; color: #111827;">
    <h1 style="font-size: 1.25rem;">{{ __('Tu cita fue registrada') }}</h1>

    <p>{{ __('Hola') }} <strong>{{ $appointment->patient->user->name ?? '' }}</strong>,</p>

    <p>{{ __('Te confirmamos que tu cita médica quedó creada en el sistema. Encontrarás el comprobante oficial adjunto en PDF.') }}</p>

    <ul style="padding-left: 1.25rem;">
        <li><strong>{{ __('Fecha') }}:</strong> {{ $appointment->date->format('d/m/Y') }}</li>
        <li><strong>{{ __('Hora') }}:</strong>
            {{ \Illuminate\Support\Str::substr($appointment->start_time, 0, 5) }}
            —
            {{ \Illuminate\Support\Str::substr($appointment->end_time, 0, 5) }}
        </li>
        <li><strong>{{ __('Doctor') }}:</strong> {{ $appointment->doctor->user->name ?? '—' }}</li>
        @if($appointment->reason)
            <li><strong>{{ __('Motivo') }}:</strong> {{ $appointment->reason }}</li>
        @endif
    </ul>

    <p style="margin-top: 1.5rem; color: #6b7280; font-size: 0.875rem;">
        {{ __('Gracias por confiar en') }} {{ config('app.name') }}.
    </p>
</body>
</html>
