<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\RequestController as CustomerRequestController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Default dashboard route - redirects based on user type
Route::get('/dashboard', function () {
    if (Auth::check()) {
        $user = Auth::user();
        // Check user_type instead of isAdmin() method
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    }
    return redirect('/login');
})->middleware('auth')->name('dashboard');

// Authentication Routes (from Breeze)
require __DIR__ . '/auth.php';

// Admin Routes Group - TEMPORARILY REMOVE MIDDLEWARE FOR TESTING
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Customer Management
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [AdminCustomerController::class, 'index'])->name('index');
        Route::get('/pending', [AdminCustomerController::class, 'pending'])->name('pending');
        Route::get('/active', [AdminCustomerController::class, 'active'])->name('active');
        Route::get('/inactive', [AdminCustomerController::class, 'inactive'])->name('inactive');
        Route::get('/{customer}', [AdminCustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [AdminCustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [AdminCustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [AdminCustomerController::class, 'destroy'])->name('destroy');

        // Actions
        Route::post('/{customer}/approve', [AdminCustomerController::class, 'approve'])->name('approve');
        Route::post('/{customer}/activate', [AdminCustomerController::class, 'activate'])->name('activate');
        Route::post('/{customer}/deactivate', [AdminCustomerController::class, 'deactivate'])->name('deactivate');
    });

    // Request Management
    Route::prefix('requests')->name('requests.')->group(function () {
        Route::get('/', [AdminRequestController::class, 'index'])->name('index');
        Route::get('/pending', [AdminRequestController::class, 'pending'])->name('pending');
        Route::get('/{request}', [AdminRequestController::class, 'show'])->name('show');
        Route::put('/{request}', [AdminRequestController::class, 'update'])->name('update');
        Route::delete('/{request}', [AdminRequestController::class, 'destroy'])->name('destroy');

        // Actions
        Route::post('/{request}/approve', [AdminRequestController::class, 'approve'])->name('approve');
        Route::post('/{request}/reject', [AdminRequestController::class, 'reject'])->name('reject');
        Route::post('/{request}/in-progress', [AdminRequestController::class, 'inProgress'])->name('in-progress');
    });
});

// Customer Routes Group - TEMPORARILY REMOVE MIDDLEWARE FOR TESTING
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::post('/notifications/{id}/read', [CustomerDashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [CustomerDashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');
    Route::get('/notifications', [CustomerDashboardController::class, 'notifications'])->name('notifications');

    // Profile
    Route::get('/profile', [CustomerProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');

    // Requests
    Route::prefix('requests')->name('requests.')->group(function () {
        Route::get('/', [CustomerRequestController::class, 'index'])->name('index');
        Route::get('/create', [CustomerRequestController::class, 'create'])->name('create');
        Route::post('/', [CustomerRequestController::class, 'store'])->name('store');
        Route::get('/activation', [CustomerRequestController::class, 'createActivationRequest'])->name('activation.create');
        Route::post('/activation', [CustomerRequestController::class, 'storeActivationRequest'])->name('activation.store');
        Route::get('/{request}', [CustomerRequestController::class, 'show'])->name('show');
        Route::delete('/{request}', [CustomerRequestController::class, 'destroy'])->name('destroy');
    });
});