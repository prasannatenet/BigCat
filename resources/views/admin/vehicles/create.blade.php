@extends('admin.layouts.app')

@section('title', 'Add Vehicle')
@section('subtitle', 'Create a new vehicle entry in your fleet.')

@section('content')
<div class="max-w-2xl mx-auto mt-6 lg:mt-10">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-slate-900">Add New Vehicle</h2>
            <p class="text-slate-500 mt-1">Fill in the vehicle details below.</p>
        </div>

        <form method="POST" action="{{ route('admin.vehicles.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-slate-700 mb-2">Type of Vehicle</label>
                    <input type="text" name="type" id="type"
                           value="{{ old('type') }}"
                           required
                           autocomplete="off"
                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                           placeholder="e.g. Bus, Car, Van">
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Vehicle Name</label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           required
                           autocomplete="off"
                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                           placeholder="e.g. Volvo AC Sleeper">
                </div>

                <div class="md:col-span-2">
                    <label for="total_seat" class="block text-sm font-medium text-slate-700 mb-2">Total Seat</label>
                    <input type="number" name="total_seat" id="total_seat"
                           value="{{ old('total_seat') }}"
                           required
                           min="1"
                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                           placeholder="e.g. 40">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="price_indian" class="block text-sm font-medium text-slate-700 mb-2">Price (Indian)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                        <input type="number" name="price_indian" id="price_indian"
                               value="{{ old('price_indian') }}"
                               required
                               step="0.01"
                               min="0"
                               class="w-full pl-9 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                               placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label for="price_foreigner" class="block text-sm font-medium text-slate-700 mb-2">Price (Foreigner)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                        <input type="number" name="price_foreigner" id="price_foreigner"
                               value="{{ old('price_foreigner') }}"
                               required
                               step="0.01"
                               min="0"
                               class="w-full pl-9 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 gap-3">
                <a href="{{ route('admin.vehicles.index') }}"
                   class="px-4 py-2.5 text-slate-600 hover:text-slate-900 rounded-xl font-medium hover:bg-slate-100 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-sm transition-all">
                    Create Vehicle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection