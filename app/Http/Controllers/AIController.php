<?php

namespace App\Http\Controllers;

use App\Domains\AI\Services\AIGeneratorService;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Plan;

class AIController extends Controller
{
    public function __construct(
        private AIGeneratorService $aiGeneratorService
    ) {}

    public function generateProductDescription(Request $request)
    {
        $user = auth()->user();
        $client = $user->client;

        // If client doesn't exist, create one with Free plan
        if (!$client) {
            $defaultPlan = Plan::where('name', 'Free')->first();
            if (!$defaultPlan) {
                return response()->json([
                    'error' => 'Default plan not found. Please contact support.'
                ], 500);
            }

            $client = Client::create([
                'user_id' => $user->id,
                'company_name' => $user->name,
                'plan_id' => $defaultPlan->id,
                'plan_expires_at' => now()->addYear(),
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'details' => 'nullable|array'
        ]);

        try {
            $description = $this->aiGeneratorService->generateProductDescription(
                $client,
                $validated
            );

            return response()->json([
                'description' => $description
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate description: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateShopGreeting(Request $request)
    {
        $client = auth()->user()->client;

        if (!$client) {
            return response()->json([
                'error' => 'Client profile not found. Please complete your profile first.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'category' => 'nullable|string'
        ]);

        try {
            $greeting = $this->aiGeneratorService->generateShopGreeting(
                $client,
                $validated
            );

            return response()->json([
                'greeting' => $greeting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate greeting: ' . $e->getMessage()
            ], 500);
        }
    }
}