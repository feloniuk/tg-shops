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
        $client = $user->client ?? Client::firstOrCreate(
            ['user_id' => $user->id],
            ['plan_id' => Plan::where('name', 'No Plan')->first()->id]
        );

        $validated = $request->validate([
            'name' => 'required|string',
            'details' => 'nullable|array'
        ]);

        $description = $this->aiGeneratorService->generateProductDescription(
            $client, 
            $validated
        );

        return response()->json([
            'description' => $description
        ]);
    }

    public function generateShopGreeting(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'category' => 'nullable|string'
        ]);

        $client = auth()->user()->client;

        $greeting = $this->aiGeneratorService->generateShopGreeting(
            $client, 
            $validated
        );

        return response()->json([
            'greeting' => $greeting
        ]);
    }
}