<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.5; color: #111827;">
    <h1 style="font-size: 1.25rem;">{{ __('Reporte de citas del día') }}</h1>
    <p><strong>{{ __('Fecha') }}:</strong> {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</p>

    @if($appointments->isEmpty())
        <p>{{ __('No hay citas programadas para este día.') }}</p>
    @else
        <p>{{ __('Total') }}: {{ $appointments->count() }} {{ __('cita(s)') }}.</p>
        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.875rem;">
            <thead>
                <tr style="background: #f3f4f6;">
                    <th style="text-align: left; padding: 8px; border: 1px solid #e5e7eb;">{{ __('Hora') }}</th>
                    <th style="text-align: left; padding: 8px; border: 1px solid #e5e7eb;">{{ __('Paciente') }}</th>
                    <th style="text-align: left; padding: 8px; border: 1px solid #e5e7eb;">{{ __('Doctor') }}</th>
                    <th style="text-align: left; padding: 8px; border: 1px solid #e5e7eb;">{{ __('Estado') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appt)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">
                            {{ \Illuminate\Support\Str::substr($appt->start_time, 0, 5) }}
                        </td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">
                            {{ $appt->patient->user->name ?? '—' }}
                        </td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">
                            {{ $appt->doctor->user->name ?? '—' }}
                        </td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ ucfirst($appt->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="margin-top: 1.5rem; color: #6b7280; font-size: 0.875rem;">{{ config('app.name') }}</p>
</body>
</html>
