<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.5; color: #111827;">
    <h1 style="font-size: 1.25rem;">{{ __('Reporte del día — todas las agendas') }}</h1>
    <p><strong>{{ __('Fecha') }}:</strong> {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</p>

    @if($appointments->isEmpty())
        <p style="font-size: 1.05rem; margin-top: 1.5rem;">
            <strong>{{ __('Día libre') }}</strong> — {{ __('no hay citas programadas para hoy en el sistema.') }}
        </p>
    @else
        <p>{{ __('Total en clínica') }}: <strong>{{ $appointments->count() }}</strong> {{ __('cita(s)') }}
            · {{ __('Médicos con agenda') }}: <strong>{{ $appointments->groupBy('doctor_id')->count() }}</strong>
        </p>

        @php
            $doctorGroups = $appointments->groupBy('doctor_id')->sortBy(function ($appts) {
                return mb_strtolower($appts->first()->doctor->user->name ?? '');
            });
        @endphp

        @foreach($doctorGroups as $appts)
            @php
                $doc = $appts->first()->doctor;
                $docName = $doc->user->name ?? __('Médico');
                $n = $appts->count();
                $sorted = $appts->sortBy('start_time');
            @endphp
            <section style="margin-top: 1.75rem; padding: 1rem 1rem 1.25rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <h2 style="font-size: 1.1rem; margin: 0 0 0.35rem 0; color: #111827;">
                    {{ $docName }}
                </h2>
                @if($doc->speciality)
                    <p style="margin: 0 0 0.5rem 0; font-size: 0.875rem; color: #6b7280;">
                        {{ $doc->speciality->name }}
                    </p>
                @endif
                <p style="margin: 0 0 0.75rem 0; font-weight: 600; color: #1d4ed8;">
                    {{ __('Tiene') }} {{ $n }} {{ $n === 1 ? __('cita') : __('citas') }} {{ __('hoy') }}.
                    {{ __('Resumen') }}:
                </p>
                <ol style="margin: 0; padding-left: 1.25rem; font-size: 0.9rem;">
                    @foreach($sorted as $appt)
                        <li style="margin-bottom: 0.35rem;">
                            <strong>{{ \Illuminate\Support\Str::substr($appt->start_time, 0, 5) }}</strong>
                            — {{ $appt->patient->user->name ?? '—' }}
                            @if($appt->reason)
                                <span style="color: #6b7280;">({{ \Illuminate\Support\Str::limit($appt->reason, 60) }})</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
                <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.8rem;">
                    <thead>
                        <tr style="background: #e5e7eb;">
                            <th style="text-align: left; padding: 6px 8px; border: 1px solid #d1d5db;">{{ __('Hora') }}</th>
                            <th style="text-align: left; padding: 6px 8px; border: 1px solid #d1d5db;">{{ __('Paciente') }}</th>
                            <th style="text-align: left; padding: 6px 8px; border: 1px solid #d1d5db;">{{ __('Estado') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sorted as $appt)
                            <tr>
                                <td style="padding: 6px 8px; border: 1px solid #e5e7eb;">
                                    {{ \Illuminate\Support\Str::substr($appt->start_time, 0, 5) }}
                                </td>
                                <td style="padding: 6px 8px; border: 1px solid #e5e7eb;">
                                    {{ $appt->patient->user->name ?? '—' }}
                                </td>
                                <td style="padding: 6px 8px; border: 1px solid #e5e7eb;">{{ ucfirst($appt->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endforeach
    @endif

    <p style="margin-top: 1.5rem; color: #6b7280; font-size: 0.875rem;">{{ config('app.name') }}</p>
</body>
</html>
