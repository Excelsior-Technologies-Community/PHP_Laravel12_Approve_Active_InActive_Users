@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3><i class="bi bi-person-plus me-2"></i> Create Your Account</h3>
                    <p class="mb-0">Join our platform and submit your first request</p>
                </div>

                <div class="card-body p-5">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1"></i> Full Name
                                </label>
                                <input id="name" type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" 
                                       placeholder="Enter your full name" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    <i class="bi bi-telephone me-1"></i> Phone Number
                                </label>
                                <input id="phone" type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone') }}" 
                                       placeholder="Enter your phone number">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-1"></i> Email Address
                            </label>
                            <input id="email" type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" 
                                   placeholder="Enter your email address" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1"></i> Password
                                </label>
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       placeholder="Create a strong password" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password-confirm" class="form-label">
                                    <i class="bi bi-lock-fill me-1"></i> Confirm Password
                                </label>
                                <input id="password-confirm" type="password" class="form-control" 
                                       name="password_confirmation" 
                                       placeholder="Confirm your password" required>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Important:</strong> After registration, your account will be pending approval. 
                            An activation request will be automatically submitted. You will be notified once your account is approved.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus me-2"></i> Register Now
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <p class="mb-0">
                                Already have an account? 
                                <a href="{{ route('login') }}" class="text-decoration-none">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Login here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection