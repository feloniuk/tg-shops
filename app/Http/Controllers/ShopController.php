<?php

namespace App\Http\Controllers;

use App\Domains\Shop\Services\ShopCreationService;
use App\Domains\Shop\Repositories\ShopRepository;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ShopController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ShopCreationService $shopCreationService,
        private ShopRepository $shopRepository
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;

        // Auto-create client if doesn't exist
        if (!$client) {
            $freePlan = \App\Models\Plan::whereIn('name', ['Free', 'No Plan'])->first();

            if (!$freePlan) {
                return redirect()->route('home')->with('error', 'Please contact support to activate your account.');
            }

            $client = \App\Models\Client::create([
                'user_id' => $user->id,
                'company_name' => $user->name,
                'plan_id' => $freePlan->id,
                'plan_expires_at' => now()->addYear(),
            ]);

            $user->load('client');
        }

        $shops = $this->shopRepository->findByClientId($client->id);

        // Return JSON for API requests, view for web
        if ($request->wantsJson()) {
            return response()->json($shops);
        }

        return view('shops.index', compact('shops', 'client'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'telegram_bot_token' => 'nullable|string',
            'welcome_message' => 'nullable|string',
            'footer_message' => 'nullable|string'
        ]);

        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Client profile not found. Please complete your profile.',
                ], 403);
            }
            return redirect()->back()->with('error', 'Client profile not found.');
        }

        try {
            $shop = $this->shopCreationService->createShop($client, $validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Shop created successfully',
                    'shop' => $shop
                ], 201);
            }

            return redirect()->route('shops.index')->with('success', 'Shop created successfully!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Shop creation failed',
                    'error' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', 'Failed to create shop: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $shopId)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'telegram_bot_token' => 'nullable|string',
            'welcome_message' => 'nullable|string',
            'footer_message' => 'nullable|string'
        ]);

        $shop = Shop::findOrFail($shopId);
        $this->authorize('update', $shop);

        $shop->update($validated);

        return response()->json([
            'message' => 'Shop updated successfully',
            'shop' => $shop
        ]);
    }
}