@extends('admin.layouts.app')

@section('title', 'Edit National Park')
@section('subtitle', 'Update national park details.')

@section('content')
<div class="max-w-2xl mx-auto mt-6 lg:mt-10">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-slate-900">Edit National Park</h2>
            <p class="text-slate-500 mt-1">Update the national park details below.</p>
        </div>

        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.national-parks.update', $nationalPark) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Park Name</label>
                <input type="text" name="name" id="name"
                       value="{{ old('name', $nationalPark->name) }}"
                       required
                       autocomplete="off"
                       class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-slate-700 mb-2">Location</label>
                <input type="text" name="location" id="location"
                       value="{{ old('location', $nationalPark->location) }}"
                       required
                       autocomplete="off"
                       class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                <textarea name="description" id="description"
                          rows="4"
                          class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">{{ old('description', $nationalPark->description) }}</textarea>
            </div>

            <div>
                <p class="mb-2 text-sm font-medium text-slate-700">Park images (up to 5)</p>
                <div class="mb-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($nationalPark->images as $image)
                        <div class="rounded-xl border border-slate-200 p-2">
                            <img src="{{ Storage::disk('public')->url($image->path) }}" alt="{{ $nationalPark->name }}" class="h-28 w-full rounded-lg object-cover">
                            <label class="mt-2 block text-xs text-slate-600">Position
                                <input type="number" name="image_positions[{{ $image->id }}]" value="{{ old('image_positions.'.$image->id, $image->position) }}" min="1" max="5" class="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1">
                            </label>
                            <label class="mt-2 flex items-center gap-2 text-xs text-red-600"><input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="!h-4 !w-4"> Remove image</label>
                        </div>
                    @endforeach
                </div>
                <label for="images" class="mb-2 block text-sm font-medium text-slate-700">Add images</label>
                <input type="file" name="images[]" id="images" accept="image/jpeg,image/png,image/webp" multiple class="w-full rounded-xl border border-slate-300 px-4 py-3">
                <p class="mt-1 text-xs text-slate-500">JPG, PNG, or WebP; up to 5 MB each. Save to apply positions.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="entry_fee_indian" class="block text-sm font-medium text-slate-700 mb-2">Entry Fee (Indian)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                        <input type="number" name="entry_fee_indian" id="entry_fee_indian"
                               value="{{ old('entry_fee_indian', $nationalPark->entry_fee_indian) }}"
                               step="0.01"
                               min="0"
                               class="w-full pl-9 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label for="entry_fee_foreigner" class="block text-sm font-medium text-slate-700 mb-2">Entry Fee (Foreigner)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                        <input type="number" name="entry_fee_foreigner" id="entry_fee_foreigner"
                               value="{{ old('entry_fee_foreigner', $nationalPark->entry_fee_foreigner) }}"
                               step="0.01"
                               min="0"
                               class="w-full pl-9 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
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
                    Update National Park
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
