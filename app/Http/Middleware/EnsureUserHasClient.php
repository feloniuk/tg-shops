<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Client;
use App\Models\Plan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user->client) {
            // Создаем клиента, если его нет
            $client = Client::create([
                'user_id' => $user->id,
                'plan_id' => Plan::where('name', 'No Plan')->first()->id
            ]);
        }

        return $next($request);
    }
}