<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SafariBookingRequest;
use Illuminate\Contracts\View\View;

class BookingRequestController extends Controller
{
    public function index(): View
    {
        $bookingRequests = SafariBookingRequest::query()
            ->with(['nationalPark', 'slots.zone'])
            ->latest()
            ->paginate(15);

        return view('admin.booking-requests.index', compact('bookingRequests'));
    }

    public function show(SafariBookingRequest $bookingRequest): View
    {
        $bookingRequest->load(['nationalPark', 'zone', 'slots.zone']);

        return view('admin.booking-requests.show', compact('bookingRequest'));
    }
}
