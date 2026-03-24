<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.5; color: #111827;">
    <h1 style="font-size: 1.25rem;">{{ __('Reporte matutino — citas por médico') }}</h1>
    <p><strong>{{ __('Fecha') }}:</strong> {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</p>

    @if($appointments->isEmpty())
        <p style="font-size: 1.05rem; margin-top: 1.5rem;">
            <strong>{{ __('Día libre') }}</strong> — {{ __('no hay citas programadas para hoy en el sistema.') }}
        </p>
    @else
        <p>{{ __('Total general') }}: <strong>{{ $appointments->count() }}</strong> {{ __('cita(s)') }}.</p>

        @foreach($appointments->groupBy('doctor_id') as $doctorId => $appts)
            @php $doctorUser = $appts->first()->doctor->user; @endphp
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                <h2 style="font-size: 1.05rem; margin: 0 0 0.5rem 0;">
                    {{ $doctorUser->name ?? __('Médico') }}
                </h2>
                <p style="margin: 0 0 0.75rem 0; color: #374151;">
                    <strong>{{ $appts->count() }}</strong>
                    {{ $appts->count() === 1 ? __('cita') : __('citas') }}:
                    @foreach($appts as $idx => $a)
                        {{ \Illuminate\Support\Str::substr($a->start_time, 0, 5) }}
                        ({{ $a->patient->user->name ?? '—' }})@if(!$loop->last), @endif
                    @endforeach
                </p>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: #f3f4f6;">
                            <th style="text-align: left; padding: 8px; border: 1px solid #e5e7eb;">{{ __('Hora') }}</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #e5e7eb;">{{ __('Paciente') }}</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #e5e7eb;">{{ __('Estado') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appts->sortBy('start_time') as $appt)
                            <tr>
                                <td style="padding: 8px; border: 1px solid #e5e7eb;">
                                    {{ \Illuminate\Support\Str::substr($appt->start_time, 0, 5) }}
                                </td>
                                <td style="padding: 8px; border: 1px solid #e5e7eb;">
                                    {{ $appt->patient->user->name ?? '—' }}
                                </td>
                                <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ ucfirst($appt->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

    <p style="margin-top: 1.5rem; color: #6b7280; font-size: 0.875rem;">{{ config('app.name') }}</p>
</body>
</html>
