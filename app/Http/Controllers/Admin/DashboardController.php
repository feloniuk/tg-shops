<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Shop;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_clients' => Client::count(),
            'total_shops' => Shop::count(),
            'total_orders' => Order::count(),
            'revenue' => Order::sum('total_amount'),
            'active_shops' => Shop::where('status', 'active')->count(),
        ];

        $recentUsers = User::latest()->limit(10)->get();
        $recentOrders = Order::with('shop')->latest()->limit(10)->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentOrders' => $recentOrders
        ]);
    }
}