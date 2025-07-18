<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Asset;
use App\Models\User;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['asset', 'user', 'assignedTo'])
            ->filter(request()->all())
            ->latest()
            ->paginate(20);

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $assets = Asset::all();
        $users = User::all();
        return view('tickets.create', compact('assets', 'users'));
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = Ticket::create($request->validated() + [
            'ticket_number' => 'TICK-' . strtoupper(uniqid()),
            'user_id' => auth()->id(),
            'status' => 'Open'
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['asset', 'user', 'assignedTo', 'comments.user']);
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $assets = Asset::all();
        $users = User::all();
        return view('tickets.edit', compact('ticket', 'assets', 'users'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $ticket->update($request->validated());
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Comment added successfully');
    }

    public function resolve(Request $request, Ticket $ticket)
    {
        $request->validate([
            'resolution' => 'required|string'
        ]);

        $ticket->update([
            'status' => 'Resolved',
            'resolution' => $request->resolution,
            'resolved_at' => now()
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket resolved successfully');
    }
}
