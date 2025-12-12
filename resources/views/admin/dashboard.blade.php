@extends('layouts.admin')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    @php
    $cards = [
        [
            'title' => 'Total Users',
            'value' => $stats['total_users'],
            'icon' => 'heroicon-o-users',
            'color' => 'text-blue-500'
        ],
        [
            'title' => 'Active Shops',
            'value' => $stats['active_shops'],
            'icon' => 'heroicon-o-shopping-bag',
            'color' => 'text-green-500'
        ],
        // Другие карты статистики
    ];
    @endphp

    @foreach($cards as $card)
        <x-admin-stat-card 
            :title="$card['title']" 
            :value="$card['value']"
            :icon="$card['icon']"
            :color="$card['color']"
        />
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
    <x-admin-recent-users :users="$recentUsers" />
    <x-admin-recent-orders :orders="$recentOrders" />
</div>
@endsection