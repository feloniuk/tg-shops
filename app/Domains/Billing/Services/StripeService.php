<?php

namespace App\Domains\Billing\Services;

use App\Models\Client;
use App\Models\Plan;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class StripeService
{
    private $stripe;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.key'));
    }

    public function createCheckoutSession(Client $client, Plan $plan): Session
    {
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => config('services.stripe.default_currency'),
                        'unit_amount' => $plan->price * 100, // Cents
                        'product_data' => [
                            'name' => $plan->name . ' Plan',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => URL::route('billing.success', ['client_id' => $client->id]),
                'cancel_url' => URL::route('billing.cancel', ['client_id' => $client->id]),
                'client_reference_id' => $client->id,
                'metadata' => [
                    'client_id' => $client->id,
                    'plan_id' => $plan->id
                ]
            ]);

            return $session;
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Session Error', [
                'client_id' => $client->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function handleWebhook(string $payload, string $signatureHeader)
    {
        try {
            $event = Webhook::constructEvent(
                $payload, 
                $signatureHeader, 
                config('services.stripe.webhook_secret')
            );

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $this->handleSuccessfulCheckout($session);
                    break;
                case 'invoice.paid':
                    $invoice = $event->data->object;
                    $this->handleInvoicePaid($invoice);
                    break;
                // Добавить другие обработчики webhook
            }
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function handleSuccessfulCheckout(object $session)
    {
        $clientId = $session->metadata->client_id;
        $planId = $session->metadata->plan_id;

        $client = Client::findOrFail($clientId);
        $plan = Plan::findOrFail($planId);

        // Обновляем план и дату истечения
        $client->update([
            'plan_id' => $plan->id,
            'plan_expires_at' => now()->addMonth()
        ]);

        Log::info('Successful Stripe Checkout', [
            'client_id' => $clientId,
            'plan_id' => $planId
        ]);
    }

    private function handleInvoicePaid(object $invoice)
    {
        // Логика обработки оплаченного счета
        Log::info('Invoice Paid', [
            'invoice_id' => $invoice->id
        ]);
    }
}