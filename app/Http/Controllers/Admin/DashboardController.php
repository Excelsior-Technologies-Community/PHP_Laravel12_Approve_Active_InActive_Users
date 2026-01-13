<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;

class DashboardController extends Controller
{
    public function index()
    {
        // Customer statistics
        $totalCustomers = User::customers()->count();
        $pendingCustomers = User::customers()->pending()->count();
        $activeCustomers = User::customers()->active()->count();
        
        // Request statistics
        $totalRequests = Request::count();
        $pendingRequests = Request::pending()->count();
        $approvedRequests = Request::approved()->count();
        $rejectedRequests = Request::rejected()->count();
        
        // Recent requests
        $recentRequests = Request::with('user')
            ->latest()
            ->take(5)
            ->get();
        
        // Recent registrations
        $recentCustomers = User::customers()
            ->withCount(['requests'])
            ->latest()
            ->take(5)
            ->get();
        
        // Request types distribution
        $requestTypes = Request::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->type => $item->count];
            });

        return view('admin.dashboard', compact(
            'totalCustomers',
            'pendingCustomers',
            'activeCustomers',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'recentRequests',
            'recentCustomers',
            'requestTypes'
        ));
    }
}