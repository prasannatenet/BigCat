@extends('admin.layouts.app')

@section('title', 'Coupons')
@section('subtitle', 'Create and manage promotional coupons.')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Coupons</h1>
            <p class="mt-1 text-slate-500">Create and manage promotional coupons.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-blue-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create coupon
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">Coupon name</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">Type</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">Offer</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">Users used / limit</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">Valid dates</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($coupons as $coupon)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $coupon->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ ['percentage' => 'Percentage off', 'fixed_amount' => 'Fixed amount off', 'custom' => 'Custom'][$coupon->type] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                @if ($coupon->type === 'percentage')
                                    {{ rtrim(rtrim($coupon->discount_value, '0'), '.') }}% off
                                @elseif ($coupon->type === 'fixed_amount')
                                    ₹{{ number_format((float) $coupon->discount_value, 2) }} off
                                @else
                                    {{ $coupon->custom_text }}
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-700">
                                {{ number_format((int) $coupon->used_user_count) }} / {{ $coupon->usage_limit ?? 'Unlimited' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                                {{ $coupon->starts_at->format('M j, Y') }} – {{ $coupon->ends_at->format('M j, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <p class="font-medium text-slate-900">No coupons yet</p>
                                <p class="mt-1 text-sm text-slate-500">Create your first coupon to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $coupons->links() }}
    </div>
</div>
@endsection
