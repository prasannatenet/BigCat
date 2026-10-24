@extends('admin.layouts.app')

@section('title', 'Booking Requests')
@section('subtitle', 'Review availability and submitted safari booking requests.')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Booking Requests</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $bookingRequests->total() }} {{ $bookingRequests->total() === 1 ? 'request' : 'requests' }}</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1020px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Coupon</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Availability</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Request status</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Submitted</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bookingRequests as $bookingRequest)
                        @php
                            $availableSlotCount = $bookingRequest->slots->where('was_available', true)->count();
                            $requestedSlotCount = $bookingRequest->slots->count();
                            $allSlotsAvailable = $requestedSlotCount > 0
                                ? $availableSlotCount === $requestedSlotCount
                                : $bookingRequest->was_available;
                            $noSlotsAvailable = $requestedSlotCount > 0
                                ? $availableSlotCount === 0
                                : ! $bookingRequest->was_available;
                        @endphp
                        <tr class="align-middle hover:bg-slate-50/70">
                            <td class="px-5 py-4 text-sm">
                                <p class="font-semibold text-slate-900">{{ $bookingRequest->name }}</p>
                                <a class="mt-1 block text-blue-700 hover:underline" href="mailto:{{ $bookingRequest->email }}">{{ $bookingRequest->email }}</a>
                                <a class="mt-1 block text-slate-600 hover:underline" href="tel:{{ $bookingRequest->phone_country_code }}{{ $bookingRequest->phone }}">{{ $bookingRequest->phone_country_code }} {{ $bookingRequest->phone }}</a>
                                @if ($bookingRequest->phone_country)
                                    <span class="mt-1 block text-xs text-slate-500">Country: {{ $bookingRequest->phone_country }}</span>
                                @else
                                    <span class="mt-1 block text-xs text-slate-400">Country not recorded</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm">
                                @if ($bookingRequest->coupon_name)
                                    <p class="font-medium text-slate-800">{{ $bookingRequest->coupon_name }}</p>
                                    <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $bookingRequest->coupon_id ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $bookingRequest->coupon_id ? 'Applied' : 'Not applied' }}
                                    </span>
                                @else
                                    <span class="text-slate-400">None</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($requestedSlotCount > 0)
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $allSlotsAvailable ? 'bg-emerald-100 text-emerald-800' : ($noSlotsAvailable ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $availableSlotCount }} / {{ $requestedSlotCount }} available
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $bookingRequest->was_available ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $bookingRequest->was_available ? 'Available' : 'Unavailable' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $bookingRequest->request_status === 'submitted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $bookingRequest->request_status === 'submitted' ? 'Submitted' : 'Awaiting submission' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $bookingRequest->request_status === 'submitted' ? $bookingRequest->updated_at->format('M j, Y g:i A') : '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.booking-requests.show', $bookingRequest) }}" class="inline-flex items-center rounded-lg bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-slate-700">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <p class="font-semibold text-slate-900">No booking requests yet</p>
                                <p class="mt-1 text-sm text-slate-500">Booking requests will appear here after customers submit them.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($bookingRequests->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $bookingRequests->links() }}</div>
        @endif
    </div>
</div>
@endsection
