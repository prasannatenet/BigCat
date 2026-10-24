@extends('admin.layouts.app')

@section('title', 'Customers')
@section('subtitle', 'Customer names and contact details.')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Customer Details</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $customers->total() }} {{ $customers->total() === 1 ? 'customer' : 'customers' }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Email address</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Phone number</th>
                        <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Country</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $customer->name }}</td>
                            <td class="px-5 py-4 text-sm">
                                <a class="text-blue-700 hover:underline" href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <a class="text-slate-600 hover:underline" href="tel:{{ $customer->phone_country_code }}{{ $customer->phone }}">{{ $customer->phone_country_code }} {{ $customer->phone }}</a>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $customer->phone_country ?? 'Not recorded' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <p class="font-semibold text-slate-900">No customers yet</p>
                                <p class="mt-1 text-sm text-slate-500">Customer details will appear here after a booking request is submitted.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($customers->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $customers->links() }}</div>
        @endif
    </div>
</div>
@endsection
