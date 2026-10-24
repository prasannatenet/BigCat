<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NationalPark;
use App\Models\ParkZone;
use App\Models\SafariBookingRequest;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'bookingRequests' => SafariBookingRequest::query()->count(),
            'awaitingSubmission' => SafariBookingRequest::query()->where('request_status', 'pending')->count(),
            'customers' => DB::query()
                ->fromSub(
                    SafariBookingRequest::query()
                        ->select(['name', 'email', 'phone', 'phone_country', 'phone_country_code'])
                        ->distinct(),
                    'unique_customers'
                )
                ->count(),
            'nationalParks' => NationalPark::query()->count(),
            'zones' => ParkZone::query()->count(),
            'vehicles' => Vehicle::query()->count(),
        ];
        $recentBookingRequests = SafariBookingRequest::query()
            ->with(['nationalPark', 'slots.zone'])
            ->latest()
            ->latest('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('recentBookingRequests', 'stats'));
    }
}
