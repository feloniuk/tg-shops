<?php

namespace App\Http\Controllers;

use App\Domains\Billing\Services\StripeService;
use App\Models\Client;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function createCheckout(
        Request $request,
        StripeService $stripeService
    ) {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $client = Auth::user()->client;

        if (! $client) {
            return response()->json([
                'message' => 'Client profile not found. Please complete your profile first.',
            ], 403);
        }

        $plan = Plan::findOrFail($validated['plan_id']);

        try {
            $session = $stripeService->createCheckoutSession($client, $plan);

            return response()->json([
                'checkout_url' => $session->url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Не удалось создать сессию оплаты',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function handleWebhook(
        Request $request,
        StripeService $stripeService
    ) {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $stripeService->handleWebhook($payload, $sigHeader);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function successPayment(Client $client)
    {
        // Логика после успешной оплаты
        return view('billing.success', compact('client'));
    }

    public function cancelPayment(Client $client)
    {
        // Логика отмены оплаты
        return view('billing.cancel', compact('client'));
    }
}
