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

    public function index()
    {
        $client = Auth::user()->client;
        $shops = $this->shopRepository->findByClientId($client->id);

        return response()->json($shops);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'telegram_bot_token' => 'nullable|string',
            'welcome_message' => 'nullable|string',
            'footer_message' => 'nullable|string'
        ]);

        $client = Auth::user()->client;

        try {
            $shop = $this->shopCreationService->createShop($client, $validated);

            return response()->json([
                'message' => 'Shop created successfully',
                'shop' => $shop
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Shop creation failed',
                'error' => $e->getMessage()
            ], 400);
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