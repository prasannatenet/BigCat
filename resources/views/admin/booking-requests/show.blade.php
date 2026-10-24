@extends('admin.layouts.app')

@section('title', 'Booking Request Details')
@section('subtitle', 'Complete customer and safari request details.')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Booking Request #{{ $bookingRequest->id }}</h1>
            <p class="mt-1 text-sm text-slate-500">Submitted {{ $bookingRequest->request_status === 'submitted' ? $bookingRequest->updated_at->format('M j, Y g:i A') : 'not yet' }}</p>
        </div>
        <a href="{{ route('admin.booking-requests.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to booking requests</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">
            <h2 class="text-base font-bold text-slate-900">Customer details</h2>
            <dl class="mt-4 space-y-4">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Name</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $bookingRequest->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Email</dt>
                    <dd class="mt-1 break-all text-sm"><a class="text-blue-700 hover:underline" href="mailto:{{ $bookingRequest->email }}">{{ $bookingRequest->email }}</a></dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</dt>
                    <dd class="mt-1 text-sm"><a class="text-slate-700 hover:underline" href="tel:{{ $bookingRequest->phone_country_code }}{{ $bookingRequest->phone }}">{{ $bookingRequest->phone_country_code }} {{ $bookingRequest->phone }}</a></dd>
                    @if ($bookingRequest->phone_country)
                        <dd class="mt-1 text-xs text-slate-500">{{ $bookingRequest->phone_country }}</dd>
                    @endif
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-base font-bold text-slate-900">Safari details</h2>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">National park</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $bookingRequest->nationalPark->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Safari mode</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $bookingRequest->safari_mode }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Request status</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $bookingRequest->request_status === 'submitted' ? 'Submitted' : 'Awaiting submission' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Created</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">{{ $bookingRequest->created_at->format('M j, Y g:i A') }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-3">
            <h2 class="text-base font-bold text-slate-900">Selected dates, zones, and slots</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[650px] text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="py-3 pr-4">Date</th>
                            <th class="py-3 pr-4">Zone</th>
                            <th class="py-3 pr-4">Slot</th>
                            <th class="py-3">Availability</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse ($bookingRequest->slots->sortBy(fn ($slot): string => $slot->safari_date->format('Y-m-d').'|'.$slot->safari_shift) as $slot)
                            <tr>
                                <td class="py-3 pr-4">{{ $slot->safari_date->format('M j, Y') }}</td>
                                <td class="py-3 pr-4 font-medium text-slate-900">{{ $slot->zone->name }}</td>
                                <td class="py-3 pr-4">{{ $slot->safari_shift }}</td>
                                <td class="py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $slot->was_available ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $slot->was_available ? 'Available' : 'Unavailable' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-500">No visit details are attached to this request.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
