<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\SafariBookingRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index(): View
    {
        $usageCountQuery = SafariBookingRequest::query()
            ->selectRaw('COUNT(DISTINCT LOWER(email))')
            ->whereColumn('safari_booking_requests.coupon_id', 'coupons.id');
        $coupons = Coupon::query()
            ->select('coupons.*')
            ->selectSub($usageCountQuery, 'used_user_count')
            ->latest()
            ->paginate(10);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed_amount,custom'],
            'discount_value' => [
                'exclude_if:type,custom',
                'required_if:type,percentage,fixed_amount',
                'nullable',
                'numeric',
                'min:0.01',
                Rule::when($request->input('type') === 'percentage', ['max:100']),
            ],
            'custom_text' => [
                'exclude_unless:type,custom',
                'required_if:type,custom',
                'nullable',
                'string',
                'max:2000',
            ],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['required', 'date_format:Y-m-d'],
            'ends_at' => ['required', 'date_format:Y-m-d', 'after_or_equal:starts_at'],
        ]);

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('status', 'Coupon created successfully.');
    }
}
