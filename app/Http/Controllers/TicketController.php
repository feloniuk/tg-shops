<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\Support\Services\TicketService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function store(
        Request $request, 
        TicketService $ticketService
    ) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'in:low,medium,high,critical',
            'attachments' => 'nullable|array'
        ]);

        $client = Auth::user()->client;

        try {
            $ticket = $ticketService->createTicket($client, $validated);

            return response()->json([
                'message' => 'Ticket created successfully',
                'ticket' => $ticket
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function addMessage(
        Request $request, 
        Ticket $ticket, 
        TicketService $ticketService
    ) {
        $validated = $request->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array'
        ]);

        try {
            $message = $ticketService->addMessage(
                $ticket, 
                Auth::user(), 
                $validated['message'],
                $validated['attachments'] ?? null
            );

            return response()->json([
                'message' => 'Message added successfully',
                'ticket_message' => $message
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }
}