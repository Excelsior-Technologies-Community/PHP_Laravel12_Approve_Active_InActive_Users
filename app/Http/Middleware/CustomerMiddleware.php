<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->user_type !== 'customer') {
            abort(403, 'Unauthorized access. Customers only.');
        }

        // Check if customer is approved and active
        if (!Auth::user()->is_approved || Auth::user()->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account is not approved or is inactive. Please contact admin.');
        }

        return $next($request);
    }
}