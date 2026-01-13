<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(HttpRequest $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'customer',
            'status' => 'pending',
            'is_approved' => false,
            'phone' => $request->phone,
        ]);

        // Auto-create account activation request
        Request::create([
            'user_id' => $user->id,
            'title' => 'Account Activation Request',
            'description' => 'New customer registration. Please review and activate account.',
            'type' => 'account_activation',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('customer.dashboard')
            ->with('info', 'Registration successful! Your account is pending approval. You will be notified once approved.');
    }
}