@props(['users'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
        Recent Users
    </h2>
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($users ?? [] as $user)
            <div class="flex items-center py-3">
                <img
                    src="{{ $user->avatar ?? 'default-avatar.png' }}"
                    alt="{{ $user->name }}"
                    class="w-10 h-10 rounded-full mr-4"
                />
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->email }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 py-3">No recent users</p>
        @endforelse
    </div>
</div>