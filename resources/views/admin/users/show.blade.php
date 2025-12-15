@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
            User Details: {{ $user->name }}
        </h2>
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
            &larr; Back to Users
        </a>
    </div>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">User Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">ID</p>
                            <p class="font-medium">{{ $user->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="font-medium">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Roles</p>
                            <p class="font-medium">
                                @forelse($user->roles ?? [] as $role)
                                    <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">{{ $role->name }}</span>
                                @empty
                                    <span class="text-gray-400">No roles</span>
                                @endforelse
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email Verified</p>
                            <p class="font-medium">
                                @if($user->email_verified_at)
                                    <span class="text-green-600">Yes</span> ({{ $user->email_verified_at->format('d.m.Y H:i') }})
                                @else
                                    <span class="text-red-600">No</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Registered</p>
                            <p class="font-medium">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($user->client)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Client Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Company Name</p>
                                <p class="font-medium">{{ $user->client->company_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Current Plan</p>
                                <p class="font-medium">
                                    @if($user->client->plan)
                                        <span class="px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">
                                            {{ $user->client->plan->name }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Plan Expires</p>
                                <p class="font-medium">
                                    {{ $user->client->plan_expires_at ? $user->client->plan_expires_at->format('d.m.Y') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Shops</p>
                                <p class="font-medium">{{ $user->client->shops->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->client->shops->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">User Shops</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($user->client->shops ?? [] as $shop)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shop->id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $shop->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 py-1 text-xs rounded
                                                        {{ $shop->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $shop->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $shop->status === 'blocked' ? 'bg-red-100 text-red-800' : '' }}
                                                    ">
                                                        {{ ucfirst($shop->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $shop->created_at->format('d.m.Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <a href="{{ route('admin.shops.show', $shop) }}" class="text-blue-600 hover:text-blue-800">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
