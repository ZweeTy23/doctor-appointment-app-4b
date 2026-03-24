<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 24px; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; line-height: 1.6; color: #0f172a; background: #ffffff;">
    <div style="max-width: 560px; margin: 0 auto;">
        <p style="margin: 0 0 8px 0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 600;">
            {{ config('app.name') }}
        </p>
        <h1 style="font-size: 1.3rem; font-weight: 700; margin: 0 0 12px 0; color: #0f172a; letter-spacing: -0.02em;">
            {{ __('Buenos días') }}, {{ $doctor->user->name ?? '' }}
        </h1>
        <p style="margin: 0 0 24px 0; font-size: 0.9rem; color: #475569;">
            {{ __('Le enviamos el resumen de su agenda para el día') }}
            <strong>{{ \Illuminate\Support\Carbon::parse($date)->locale(config('app.locale', 'es'))->isoFormat('dddd D [de] MMMM') }}</strong>.
        </p>

        @if($appointments->isEmpty())
            <div style="padding: 24px 26px; background: linear-gradient(165deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%); border-radius: 14px; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);">
                <p style="margin: 0 0 8px 0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.07em; color: #475569; font-weight: 700;">
                    {{ __('Estado de su agenda') }}
                </p>
                <p style="margin: 0 0 12px 0; font-size: 1.125rem; font-weight: 700; color: #0f172a;">
                    {{ __('Sin citas programadas para hoy') }}
                </p>
                <p style="margin: 0; font-size: 0.9rem; color: #475569; line-height: 1.7;">
                    {{ __('No constan citas médicas asignadas a su nombre para la fecha indicada. Su agenda aparece libre en el sistema.') }}
                </p>
                <p style="margin: 16px 0 0 0; font-size: 0.875rem; color: #64748b; line-height: 1.65; font-style: italic;">
                    {{ __('Si cree que se trata de un error, contacte a administración para verificar el calendario.') }}
                </p>
            </div>
        @else
            <p style="margin: 0 0 16px 0; font-size: 0.95rem; color: #334155;">
                {{ __('Tiene') }} <strong style="color: #1d4ed8;">{{ $appointments->count() }}</strong>
                {{ $appointments->count() === 1 ? __('cita programada') : __('citas programadas') }}:
            </p>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0;">
                <thead>
                    <tr style="background: #1e293b; color: #f8fafc;">
                        <th style="text-align: left; padding: 12px 14px; font-weight: 600;">{{ __('Hora') }}</th>
                        <th style="text-align: left; padding: 12px 14px; font-weight: 600;">{{ __('Paciente') }}</th>
                        <th style="text-align: left; padding: 12px 14px; font-weight: 600;">{{ __('Estado') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appt)
                        <tr style="background: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #0f172a;">
                                {{ \Illuminate\Support\Str::substr($appt->start_time, 0, 5) }}
                            </td>
                            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; color: #334155;">
                                {{ $appt->patient->user->name ?? '—' }}
                            </td>
                            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; color: #64748b;">
                                {{ ucfirst($appt->status) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="margin-top: 18px; font-size: 0.8125rem; color: #64748b;">
                {{ __('Le deseamos un excelente día de trabajo.') }}
            </p>
        @endif

        <p style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 0.8125rem; color: #94a3b8;">
            {{ __('Notificación automática —') }} <strong>{{ config('app.name') }}</strong>
        </p>
    </div>
</body>
</html>
