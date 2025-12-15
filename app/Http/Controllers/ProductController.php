<?php

namespace App\Http\Controllers;

use App\Domains\Product\Services\ProductCreationService;
use App\Domains\Product\Repositories\ProductRepository;
use App\Models\Shop;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductCreationService $productCreationService,
        private ProductRepository $productRepository
    ) {}

    public function index(Request $request, Shop $shop)
    {
        $products = $this->productRepository->findByShopId($shop->id);

        if ($request->wantsJson()) {
            return response()->json($products);
        }

        return view('products.index', compact('shop', 'products'));
    }

    public function store(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:shop_categories,id',
            'characteristics' => 'nullable|array',
            'image' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0'
        ]);

        try {
            $product = $this->productCreationService->createProduct($shop, $validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Product created successfully',
                    'product' => $product
                ], 201);
            }

            return redirect()->route('products.index', $shop)->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Product creation failed',
                    'error' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }
}