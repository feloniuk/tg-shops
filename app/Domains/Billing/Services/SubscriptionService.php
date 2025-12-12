<?php

namespace App\Domains\Billing\Services;

use App\Models\Client;
use App\Models\Plan;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function changePlan(Client $client, Plan $newPlan): bool
    {
        // Проверка возможности смены тарифа
        if (!$this->canChangePlan($client, $newPlan)) {
            return false;
        }

        // Логика биллинга (можно интегрировать со Stripe later)
        try {
            $client->update([
                'plan_id' => $newPlan->id,
                'plan_expires_at' => now()->addMonth()
            ]);

            // Логирование изменения тарифа
            Log::info('Plan changed', [
                'client_id' => $client->id,
                'old_plan' => $client->plan->name,
                'new_plan' => $newPlan->name
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Plan change failed', [
                'client_id' => $client->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    private function canChangePlan(Client $client, Plan $newPlan): bool
    {
        // Проверка количества магазинов
        $currentShopsCount = $client->shops()->count();
        if ($currentShopsCount > $newPlan->max_shops) {
            return false;
        }

        // Проверка количества продуктов
        $currentProductsCount = $client->shops()->flatMap(function ($shop) {
            return $shop->products;
        })->count();
        if ($currentProductsCount > $newPlan->max_products) {
            return false;
        }

        return true;
    }
}