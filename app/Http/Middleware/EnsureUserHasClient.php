<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Models\Plan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user->client) {
            // Создаем клиента, если его нет
            $freePlan = Plan::whereIn('name', ['Free', 'No Plan'])->first();

            if (! $freePlan) {
                abort(500, 'Free plan not found. Please run database seeders.');
            }

            $client = Client::create([
                'user_id' => $user->id,
                'company_name' => $user->name,
                'plan_id' => $freePlan->id,
                'plan_expires_at' => now()->addYear(),
            ]);

            $user->load('client');
        }

        return $next($request);
    }
}
