<?php 

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Domains\Telegram\Services\WebAppService;
use Illuminate\Http\Request;

class WebAppController extends Controller
{
    public function getProducts(
        Shop $shop, 
        WebAppService $webAppService,
        Request $request
    ) {
        $query = $request->input('query');

        $products = $query 
            ? $webAppService->searchProducts($shop, $query)
            : $webAppService->getShopProducts($shop);

        return response()->json([
            'products' => $products
        ]);
    }
}