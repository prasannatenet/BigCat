<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SafariBookingRequest;
use Illuminate\Contracts\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = SafariBookingRequest::query()
            ->select(['name', 'email', 'phone', 'phone_country', 'phone_country_code'])
            ->selectRaw('MAX(created_at) as latest_booking_at, MAX(id) as latest_booking_id')
            ->groupBy('name', 'email', 'phone', 'phone_country', 'phone_country_code')
            ->orderByDesc('latest_booking_at')
            ->orderByDesc('latest_booking_id')
            ->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }
}
