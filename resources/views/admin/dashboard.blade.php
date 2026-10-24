@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'A live overview of safari bookings and park operations.')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    @php
        $statCards = [
            ['label' => 'Booking requests', 'value' => $stats['bookingRequests'], 'description' => 'All customer requests', 'color' => 'blue'],
            ['label' => 'Awaiting submission', 'value' => $stats['awaitingSubmission'], 'description' => 'Need customer confirmation', 'color' => 'amber'],
            ['label' => 'Customers', 'value' => $stats['customers'], 'description' => 'Unique customer details', 'color' => 'violet'],
            ['label' => 'National parks', 'value' => $stats['nationalParks'], 'description' => 'Parks in the system', 'color' => 'emerald'],
            ['label' => 'Park zones', 'value' => $stats['zones'], 'description' => 'Bookable zones', 'color' => 'cyan'],
            ['label' => 'Vehicles', 'value' => $stats['vehicles'], 'description' => 'Available vehicle records', 'color' => 'rose'],
        ];
    @endphp

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="Current totals">
        @foreach ($statCards as $stat)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">{{ $stat['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($stat['value']) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $stat['description'] }}</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-3">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2" aria-labelledby="recent-bookings-heading">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 id="recent-bookings-heading" class="text-lg font-bold text-slate-900">Newest booking requests</h2>
                    <p class="mt-1 text-sm text-slate-500">The five most recently created requests.</p>
                </div>
                <a href="{{ route('admin.booking-requests.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">View all requests <span aria-hidden="true">&rarr;</span></a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Park and visits</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Received</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentBookingRequests as $bookingRequest)
                            <tr class="align-top hover:bg-slate-50/70">
                                <td class="px-5 py-4 text-sm">
                                    <a href="{{ route('admin.booking-requests.show', $bookingRequest) }}" class="font-semibold text-slate-900 hover:text-blue-700">{{ $bookingRequest->name }}</a>
                                    <a href="mailto:{{ $bookingRequest->email }}" class="mt-1 block text-xs text-slate-500 hover:text-blue-700">{{ $bookingRequest->email }}</a>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <p class="font-medium text-slate-800">{{ $bookingRequest->nationalPark->name }}</p>
                                    @foreach ($bookingRequest->slots->take(2) as $slot)
                                        <p class="mt-1 text-xs text-slate-500">{{ $slot->zone->name }} · {{ $slot->safari_shift }} · {{ $slot->safari_date->format('M j, Y') }}</p>
                                    @endforeach
                                    @if ($bookingRequest->slots->count() > 2)
                                        <p class="mt-1 text-xs font-medium text-slate-500">+ {{ $bookingRequest->slots->count() - 2 }} more visits</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $bookingRequest->request_status === 'submitted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $bookingRequest->request_status === 'submitted' ? 'Submitted' : 'Awaiting submission' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">{{ $bookingRequest->created_at->format('M j, Y g:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center">
                                    <p class="font-semibold text-slate-900">No booking requests yet</p>
                                    <p class="mt-1 text-sm text-slate-500">New customer requests will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="quick-actions-heading">
            <h2 id="quick-actions-heading" class="text-lg font-bold text-slate-900">Quick actions</h2>
            <p class="mt-1 text-sm text-slate-500">Common admin tasks.</p>
            <div class="mt-5 grid gap-3">
                <a href="{{ route('admin.booking-requests.index') }}" class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-800 transition hover:bg-blue-100">Review booking requests <span class="float-right" aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('admin.slots.index') }}" class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-100">Manage slot availability <span class="float-right" aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('admin.vehicles.create') }}" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Add a vehicle <span class="float-right" aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('admin.email-settings.index') }}" class="rounded-xl border border-violet-100 bg-violet-50 px-4 py-3 text-sm font-semibold text-violet-800 transition hover:bg-violet-100">Configure email <span class="float-right" aria-hidden="true">&rarr;</span></a>
            </div>
        </aside>
    </div>
</div>
@endsection
