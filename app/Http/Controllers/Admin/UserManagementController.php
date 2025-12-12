<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Фильтрация
        if ($request->has('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%");
        }

        // Сортировка
        $query->orderBy(
            $request->get('sort', 'created_at'), 
            $request->get('direction', 'desc')
        );

        $users = $query->paginate(20);

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