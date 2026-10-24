@extends('admin.layouts.app')

@section('title', 'Create Coupon')
@section('subtitle', 'Set up a discount or custom promotional offer.')

@section('content')
<div class="mx-auto mt-6 max-w-2xl lg:mt-10">
    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-slate-900">Create a coupon</h2>
            <p class="mt-1 text-slate-500">Enter the coupon details and the dates it will be available.</p>
        </div>

        <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-6"
              x-data="{ couponType: @js(old('type', 'percentage')), startsAt: @js(old('starts_at', '')) }">
            @csrf

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Coupon name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       required maxlength="255" autocomplete="off"
                       class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g. Summer Safari Special">
            </div>

            <div>
                <label for="type" class="mb-2 block text-sm font-medium text-slate-700">Type of coupon</label>
                <select name="type" id="type" required x-model="couponType"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                    <option value="percentage">Percentage off</option>
                    <option value="fixed_amount">Fixed amount off</option>
                    <option value="custom">Custom (admin text)</option>
                </select>
            </div>

            <div x-show="couponType !== 'custom'">
                <label for="discount_value" class="mb-2 block text-sm font-medium text-slate-700">Discount value</label>
                <div class="relative">
                    <input type="number" name="discount_value" id="discount_value"
                           value="{{ old('discount_value') }}" min="0.01" step="0.01"
                           :max="couponType === 'percentage' ? 100 : null"
                           :required="couponType !== 'custom'"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-16 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter discount">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-slate-500"
                          x-text="couponType === 'percentage' ? '%' : '₹'"></span>
                </div>
                <p class="mt-2 text-xs text-slate-500">Percentage discounts can be from 0.01% to 100%.</p>
            </div>

            <div x-show="couponType === 'custom'">
                <label for="custom_text" class="mb-2 block text-sm font-medium text-slate-700">Custom offer text</label>
                <textarea name="custom_text" id="custom_text" rows="4" maxlength="2000"
                          :required="couponType === 'custom'"
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                          placeholder="Describe the offer for customers">{{ old('custom_text') }}</textarea>
                <p class="mt-2 text-xs text-slate-500">Write the custom offer details customers should see.</p>
            </div>

            <div>
                <label for="usage_limit" class="mb-2 block text-sm font-medium text-slate-700">Maximum users</label>
                <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}"
                       min="1" step="1"
                       class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                       placeholder="Leave blank for no limit">
                <p class="mt-2 text-xs text-slate-500">The coupon can be used by this many different customers. Leave blank for unlimited use.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="starts_at" class="mb-2 block text-sm font-medium text-slate-700">Starting date</label>
                    <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                           required x-model="startsAt"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="ends_at" class="mb-2 block text-sm font-medium text-slate-700">Ending date</label>
                    <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at') }}"
                           required
                           :min="startsAt"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('admin.coupons.index') }}"
                   class="rounded-xl px-4 py-2.5 font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900">
                    Cancel
                </a>
                <button type="submit"
                        class="rounded-xl bg-blue-600 px-6 py-2.5 font-medium text-white shadow-sm transition-all hover:bg-blue-700">
                    Create coupon
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
