<?php

namespace App\Domains\Support\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Client;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketService
{
    public function createTicket(Client $client, array $data): Ticket
    {
        // Проверка количества открытых тикетов
        $openTicketsCount = $client->tickets()->where('status', 'open')->count();
        if ($openTicketsCount >= 5) {
            throw ValidationException::withMessages([
                'tickets' => 'Превышено максимальное количество открытых тикетов'
            ]);
        }

        return DB::transaction(function () use ($client, $data) {
            $ticket = Ticket::create([
                'client_id' => $client->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => 'open',
                'priority' => $data['priority'] ?? 'low'
            ]);

            // Создание первого сообщения
            $ticket->messages()->create([
                'sender_id' => $client->user_id,
                'message' => $data['description'],
                'attachments' => $data['attachments'] ?? null
            ]);

            return $ticket;
        });
    }

    public function assignManager(Ticket $ticket, User $manager): bool
    {
        if (!$manager->hasRole('manager')) {
            throw ValidationException::withMessages([
                'manager' => 'Указанный пользователь не является менеджером поддержки'
            ]);
        }

        return $ticket->update([
            'manager_id' => $manager->id,
            'status' => 'in_progress'
        ]);
    }

    public function addMessage(Ticket $ticket, User $sender, string $message, ?array $attachments = null): TicketMessage
    {
        // Проверка прав на добавление сообщения
        if ($ticket->isClosed()) {
            throw ValidationException::withMessages([
                'ticket' => 'Нельзя добавить сообщение в закрытый тикет'
            ]);
        }

        return DB::transaction(function () use ($ticket, $sender, $message, $attachments) {
            // Обновляем статус тикета
            $ticket->update([
                'status' => $sender->hasRole('manager') ? 'in_progress' : 'open',
                'last_response_at' => now()
            ]);

            // Создаем сообщение
            return $ticket->messages()->create([
                'sender_id' => $sender->id,
                'message' => $message,
                'attachments' => $attachments
            ]);
        });
    }

    public function closeTicket(Ticket $ticket, User $closedBy): bool
    {
        return $ticket->update([
            'status' => 'closed'
        ]);
    }
}