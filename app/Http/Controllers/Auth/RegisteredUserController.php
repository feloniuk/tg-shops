<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Получаем бесплатный план (проверяем оба названия для совместимости)
        $freePlan = Plan::whereIn('name', ['Free', 'No Plan'])->first();

        if (! $freePlan) {
            // Если плана нет, создаем базовый
            $freePlan = Plan::create([
                'name' => 'Free',
                'max_shops' => 1,
                'max_products' => 10,
                'ai_enabled' => false,
                'price' => 0.00,
            ]);
        }

        // Создаем клиента при регистрации
        $client = Client::create([
            'user_id' => $user->id,
            'company_name' => $user->name,
            'plan_id' => $freePlan->id,
            'plan_expires_at' => now()->addYear(),
        ]);

        // Назначаем роль клиента
        $user->assignRole('client');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
