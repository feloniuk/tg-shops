<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Фильтрация
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Сортировка
        $query->orderBy(
            $request->get('sort', 'created_at'),
            $request->get('direction', 'desc')
        );

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users' => $users
        ]);
    }

    public function show(User $user)
    {
        $user->load(['client', 'client.shops', 'client.plan']);
        return view('admin.users.show', [
            'user' => $user
        ]);
    }

    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,blocked'
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User status updated successfully');
    }
}