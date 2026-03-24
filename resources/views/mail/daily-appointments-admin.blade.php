<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 24px; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; line-height: 1.6; color: #0f172a; background: #ffffff;">
    <div style="max-width: 640px; margin: 0 auto;">
        <h1 style="font-size: 1.35rem; font-weight: 700; margin: 0 0 8px 0; color: #0f172a; letter-spacing: -0.02em;">
            {{ __('Reporte del día — todas las agendas') }}
        </h1>
        <p style="margin: 0 0 24px 0; font-size: 0.9rem; color: #64748b;">
            <strong style="color: #334155;">{{ __('Fecha') }}:</strong>
            {{ \Illuminate\Support\Carbon::parse($date)->locale(config('app.locale', 'es'))->isoFormat('dddd D [de] MMMM [de] YYYY') }}
        </p>

        @if($doctors->isEmpty())
            <div style="padding: 20px 24px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; color: #92400e;">
                <strong>{{ __('Sin médicos registrados') }}</strong>
                — {{ __('No hay profesionales en el sistema para incluir en este reporte.') }}
            </div>
        @else
            @php
                $totalCitas = $appointments->count();
                $medicosConCita = $doctors->filter(fn ($d) => $appointments->where('doctor_id', $d->id)->isNotEmpty())->count();
            @endphp

            <div style="padding: 16px 20px; margin-bottom: 24px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                <p style="margin: 0; font-size: 0.9rem; color: #475569;">
                    <strong style="color: #0f172a;">{{ __('Resumen general') }}</strong><br>
                    {{ __('Citas programadas hoy') }}: <strong>{{ $totalCitas }}</strong>
                    · {{ __('Médicos en plantilla') }}: <strong>{{ $doctors->count() }}</strong>
                    · {{ __('Con al menos una cita') }}: <strong>{{ $medicosConCita }}</strong>
                </p>
            </div>

            @if($totalCitas === 0)
                <div style="padding: 20px 24px; margin-bottom: 28px; background: linear-gradient(180deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 12px;">
                    <p style="margin: 0 0 8px 0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #0369a1; font-weight: 600;">
                        {{ __('Estado de la clínica') }}
                    </p>
                    <p style="margin: 0; font-size: 1.05rem; font-weight: 600; color: #0c4a6e;">
                        {{ __('No hay citas programadas para hoy') }}
                    </p>
                    <p style="margin: 12px 0 0 0; font-size: 0.875rem; color: #075985; line-height: 1.65;">
                        {{ __('A continuación se listan todos los médicos de la plantilla. Cada uno figura sin citas en agenda para la fecha indicada.') }}
                    </p>
                </div>
            @endif

            @foreach($doctors as $doctor)
                @php
                    $sorted = $appointments->where('doctor_id', $doctor->id)->sortBy('start_time')->values();
                    $docName = $doctor->user->name ?? __('Médico');
                    $n = $sorted->count();
                @endphp

                <section style="margin-bottom: 24px; padding: 20px 22px; background: #fafafa; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <h2 style="font-size: 1.05rem; font-weight: 700; margin: 0 0 4px 0; color: #111827;">
                        {{ $docName }}
                    </h2>
                    @if($doctor->speciality)
                        <p style="margin: 0 0 16px 0; font-size: 0.8125rem; color: #6b7280;">
                            {{ $doctor->speciality->name }}
                        </p>
                    @else
                        <div style="margin-bottom: 16px;"></div>
                    @endif

                    @if($sorted->isEmpty())
                        <div style="padding: 18px 20px; background: #ffffff; border-radius: 10px; border: 1px dashed #cbd5e1;">
                            <p style="margin: 0 0 6px 0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; font-weight: 600;">
                                {{ __('Agenda del día') }}
                            </p>
                            <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #334155;">
                                {{ __('Sin citas programadas') }}
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 0.875rem; color: #64748b; line-height: 1.65;">
                                {{ __('Este profesional no tiene citas médicas registradas para la fecha del reporte. La agenda se encuentra disponible.') }}
                            </p>
                        </div>
                    @else
                        <p style="margin: 0 0 14px 0; font-weight: 600; color: #1d4ed8; font-size: 0.9rem;">
                            {{ __('Tiene') }} {{ $n }} {{ $n === 1 ? __('cita') : __('citas') }} {{ __('hoy') }}.
                            <span style="color: #64748b; font-weight: 500;">{{ __('Detalle') }}:</span>
                        </p>
                        <ol style="margin: 0 0 16px 0; padding-left: 1.25rem; font-size: 0.9rem; color: #374151;">
                            @foreach($sorted as $appt)
                                <li style="margin-bottom: 0.4rem;">
                                    <strong>{{ \Illuminate\Support\Str::substr($appt->start_time, 0, 5) }}</strong>
                                    — {{ $appt->patient->user->name ?? '—' }}
                                    @if($appt->reason)
                                        <span style="color: #6b7280;">({{ \Illuminate\Support\Str::limit($appt->reason, 55) }})</span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                            <thead>
                                <tr style="background: #e5e7eb;">
                                    <th style="text-align: left; padding: 8px 10px; border: 1px solid #d1d5db;">{{ __('Hora') }}</th>
                                    <th style="text-align: left; padding: 8px 10px; border: 1px solid #d1d5db;">{{ __('Paciente') }}</th>
                                    <th style="text-align: left; padding: 8px 10px; border: 1px solid #d1d5db;">{{ __('Estado') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sorted as $appt)
                                    <tr>
                                        <td style="padding: 8px 10px; border: 1px solid #e5e7eb;">
                                            {{ \Illuminate\Support\Str::substr($appt->start_time, 0, 5) }}
                                        </td>
                                        <td style="padding: 8px 10px; border: 1px solid #e5e7eb;">
                                            {{ $appt->patient->user->name ?? '—' }}
                                        </td>
                                        <td style="padding: 8px 10px; border: 1px solid #e5e7eb;">{{ ucfirst($appt->status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </section>
            @endforeach
        @endif

        <p style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 0.8125rem; color: #94a3b8;">
            {{ __('Mensaje generado automáticamente por') }} <strong>{{ config('app.name') }}</strong>.
            {{ __('No responda a este correo si no es necesario.') }}
        </p>
    </div>
</body>
</html>
