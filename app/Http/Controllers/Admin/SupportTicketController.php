<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Listar todos los tickets de soporte.
     */
    public function index()
    {
        return view('admin.tickets.index');
    }

    /**
     * Mostrar formulario para crear un nuevo ticket.
     */
    public function create()
    {
        return view('admin.tickets.create');
    }

    /**
     * Almacenar un nuevo ticket en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'priority'    => 'required|in:baja,media,alta',
        ]);

        SupportTicket::create([
            'title'       => $request->title,
            'description' => $request->description,
            'priority'    => $request->priority,
            'user_id'     => auth()->id(),
        ]);

        return redirect()
            ->route('admin.tickets.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Ticket creado',
                'text'  => 'Tu ticket de soporte ha sido registrado exitosamente.',
            ]);
    }

    /**
     * Mostrar el detalle de un ticket.
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load('user');
        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Mostrar formulario para editar un ticket (estado, prioridad, respuesta admin).
     */
    public function edit(SupportTicket $ticket)
    {
        $ticket->load('user');
        return view('admin.tickets.edit', compact('ticket'));
    }

    /**
     * Actualizar el ticket (estado, prioridad, respuesta del admin).
     */
    public function update(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'status'         => 'required|in:abierto,en_progreso,cerrado',
            'priority'       => 'required|in:baja,media,alta',
            'admin_response' => 'nullable|string|max:5000',
        ]);

        $ticket->update($data);

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Ticket actualizado',
                'text'  => 'El ticket ha sido actualizado correctamente.',
            ]);
    }

    /**
     * Eliminar un ticket de soporte.
     */
    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();

        return redirect()
            ->route('admin.tickets.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Ticket eliminado',
                'text'  => 'El ticket de soporte ha sido eliminado.',
            ]);
    }
}
