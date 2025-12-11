<?php

namespace App\Domains\Client\Repositories;

use App\Models\Client;
use Illuminate\Support\Collection;

class ClientRepository
{
    public function findByUserId(int $userId): ?Client
    {
        return Client::where('user_id', $userId)->first();
    }

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function updatePlan(Client $client, int $planId): bool
    {
        return $client->update([
            'plan_id' => $planId,
            'plan_expires_at' => now()->addMonth()
        ]);
    }
}