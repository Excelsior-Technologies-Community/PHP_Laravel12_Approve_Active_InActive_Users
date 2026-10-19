<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Request as CustomerRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Customer statistics
        $totalCustomers = User::customers()->count();
        $pendingCustomers = User::customers()->pending()->count();
        $activeCustomers = User::customers()->active()->count();
        $inactiveCustomers = User::customers()->inactive()->count();
        $kycVerifiedCustomers = User::customers()->where('kyc_status', 'verified')->count();
        $kycPendingCustomers = User::customers()->where('kyc_status', 'pending')->count();

        // Request statistics
        $totalRequests = CustomerRequest::count();
        $pendingRequests = CustomerRequest::pending()->count();
        $approvedRequests = CustomerRequest::approved()->count();
        $rejectedRequests = CustomerRequest::rejected()->count();
        $inProgressRequests = CustomerRequest::inProgress()->count();

        // Turnaround Time calculation (hours between submitted_at and processed_at for processed requests)
        $processedRequests = CustomerRequest::whereNotNull('processed_at')->whereNotNull('submitted_at')->get();
        $totalTurnaroundHours = 0;
        $processedCount = $processedRequests->count();

        foreach ($processedRequests as $req) {
            $diffHours = $req->submitted_at->diffInMinutes($req->processed_at) / 60;
            $totalTurnaroundHours += $diffHours;
        }

        $avgTurnaroundHours = $processedCount > 0 ? round($totalTurnaroundHours / $processedCount, 1) : 0;
        $avgTurnaroundFormatted = $avgTurnaroundHours > 24 
            ? round($avgTurnaroundHours / 24, 1) . ' days' 
            : ($avgTurnaroundHours > 0 ? $avgTurnaroundHours . ' hrs' : 'Under 1 hr');

        $resolutionRate = $totalRequests > 0 
            ? round((($approvedRequests + $rejectedRequests) / $totalRequests) * 100, 1) 
            : 0;

        // Past 30 Days Trend Data for Chart.js
        $trendDates = [];
        $registrationsSeries = [];
        $approvalsSeries = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $label = Carbon::today()->subDays($i)->format('M d');
            $trendDates[] = $label;

            $regCount = User::customers()
                ->whereDate('created_at', $date)
                ->count();
            $registrationsSeries[] = $regCount;

            $appCount = User::customers()
                ->whereDate('approved_at', $date)
                ->count();
            $approvalsSeries[] = $appCount;
        }

        // Recent requests
        $recentRequests = CustomerRequest::with('user')
            ->latest()
            ->take(6)
            ->get();

        // Recent registrations
        $recentCustomers = User::customers()
            ->withCount(['requests'])
            ->latest()
            ->take(6)
            ->get();

        // Request types distribution
        $requestTypes = CustomerRequest::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => $item->count];
            });

        return view('admin.dashboard', compact(
            'totalCustomers',
            'pendingCustomers',
            'activeCustomers',
            'inactiveCustomers',
            'kycVerifiedCustomers',
            'kycPendingCustomers',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'inProgressRequests',
            'avgTurnaroundFormatted',
            'resolutionRate',
            'trendDates',
            'registrationsSeries',
            'approvalsSeries',
            'recentRequests',
            'recentCustomers',
            'requestTypes'
        ));
    }
}