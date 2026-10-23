@extends('admin.layouts.app')

@section('title', 'Add National Park')
@section('subtitle', 'Create a new national park entry.')

@section('content')
<div class="max-w-2xl mx-auto mt-6 lg:mt-10">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-slate-900">Add New National Park</h2>
            <p class="text-slate-500 mt-1">Fill in the national park details below.</p>
        </div>

        <form method="POST" action="{{ route('admin.national-parks.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Park Name</label>
                <input type="text" name="name" id="name"
                       value="{{ old('name') }}"
                       required
                       autocomplete="off"
                       class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                       placeholder="e.g. Jim Corbett National Park">
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-slate-700 mb-2">Location</label>
                <input type="text" name="location" id="location"
                       value="{{ old('location') }}"
                       required
                       autocomplete="off"
                       class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                       placeholder="e.g. Uttarakhand, India">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                <textarea name="description" id="description"
                          rows="4"
                          class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                          placeholder="Describe the national park...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="entry_fee_indian" class="block text-sm font-medium text-slate-700 mb-2">Entry Fee (Indian)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                        <input type="number" name="entry_fee_indian" id="entry_fee_indian"
                               value="{{ old('entry_fee_indian') }}"
                               step="0.01"
                               min="0"
                               class="w-full pl-9 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                               placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label for="entry_fee_foreigner" class="block text-sm font-medium text-slate-700 mb-2">Entry Fee (Foreigner)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                        <input type="number" name="entry_fee_foreigner" id="entry_fee_foreigner"
                               value="{{ old('entry_fee_foreigner') }}"
                               step="0.01"
                               min="0"
                               class="w-full pl-9 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 gap-3">
                <a href="{{ route('admin.national-parks.index') }}"
                   class="px-4 py-2.5 text-slate-600 hover:text-slate-900 rounded-xl font-medium hover:bg-slate-100 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-sm transition-all">
                    Create National Park
                </button>
            </div>
        </form>
    </div>
</div>
@endsection