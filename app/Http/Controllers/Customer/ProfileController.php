<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Show profile
    public function show()
    {
        $user = Auth::user();
        return view('customer.profile.show', compact('user'));
    }

    // Edit profile
    public function edit()
    {
        $user = Auth::user();
        return view('customer.profile.edit', compact('user'));
    }

    // Update profile
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('customer.profile.show')
            ->with('success', 'Profile updated successfully!');
    }
}