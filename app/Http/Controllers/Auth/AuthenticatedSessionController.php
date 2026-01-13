<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Update last login
        $user->update(['last_login' => now()]);

        // Check user type and redirect accordingly
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // For customers, check approval and status
        if (!$user->is_approved) {
            Auth::logout();
            return back()->with('error', 'Your account is pending approval. Please wait for admin approval.');
        }

        if ($user->status !== 'active') {
            Auth::logout();
            return back()->with('error', 'Your account is inactive. Please contact admin.');
        }

        return redirect()->intended(route('customer.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}