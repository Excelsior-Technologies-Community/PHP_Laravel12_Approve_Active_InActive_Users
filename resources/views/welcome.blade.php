@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="text-center">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Welcome to Customer Request System
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                A complete platform for customers to submit requests and admins to manage them efficiently.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto mb-12">
            <!-- For Customers -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
                <div class="text-blue-600 mb-4">
                    <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">For Customers</h3>
                <p class="text-gray-600 mb-4">
                    Register, submit requests, track status, and get notifications.
                </p>
                <ul class="text-left text-gray-600 mb-6 space-y-2">
                    <li><i class="bi bi-check-circle text-green-500 mr-2"></i> Submit service requests</li>
                    <li><i class="bi bi-check-circle text-green-500 mr-2"></i> Track request status</li>
                    <li><i class="bi bi-check-circle text-green-500 mr-2"></i> Get instant notifications</li>
                    <li><i class="bi bi-check-circle text-green-500 mr-2"></i> Update your profile</li>
                </ul>
                @if(!auth()->check())
                    <a href="{{ route('register') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                        Register as Customer
                    </a>
                @elseif(auth()->user()->isCustomer())
                    <a href="{{ route('customer.dashboard') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                        Go to Dashboard
                    </a>
                @endif
            </div>

            <!-- For Admins -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
                <div class="text-purple-600 mb-4">
                    <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">For Administrators</h3>
                <p class="text-gray-600 mb-4">
                    Manage customers, approve requests, and monitor system activity.
                </p>
                <ul class="text-left text-gray-600 mb-6 space-y-2">
                    <li><i class="bi bi-check-circle text-green-500 mr-2"></i> Approve customer accounts</li>
                    <li><i class="bi bi-check-circle text-green-500 mr-2"></i> Process customer requests</li>
                    <li><i class="bi bi-check-circle text-green-500 mr-2"></i> Manage customer status</li>
                    <li><i class="bi bi-check-circle text-green-500 mr-2"></i> View detailed statistics</li>
                </ul>
                @if(!auth()->check())
                    <a href="{{ route('login') }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-purple-700 transition">
                        Admin Login
                    </a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-purple-700 transition">
                        Go to Admin Panel
                    </a>
                @endif
            </div>
        </div>

        <!-- Features -->
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-center mb-8">How It Works</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center p-4">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-person-plus text-blue-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold mb-2">1. Register</h4>
                    <p class="text-gray-600">Customer registers and account goes to pending approval</p>
                </div>
                <div class="text-center p-4">
                    <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-shield-check text-yellow-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold mb-2">2. Admin Approval</h4>
                    <p class="text-gray-600">Admin reviews and approves the customer account</p>
                </div>
                <div class="text-center p-4">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-envelope-check text-green-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold mb-2">3. Submit Requests</h4>
                    <p class="text-gray-600">Customer submits requests which admin processes</p>
                </div>
            </div>
        </div>

        <!-- Login Links -->
        <div class="mt-12 text-center">
            @if(!auth()->check())
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline">
                        Login here
                    </a>
                </p>
            @else
                <p class="text-gray-600">
                    Welcome back, {{ auth()->user()->name }}!
                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="text-blue-600 font-medium hover:underline ml-2">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </p>
            @endif
        </div>
    </div>
</div>
@endsection