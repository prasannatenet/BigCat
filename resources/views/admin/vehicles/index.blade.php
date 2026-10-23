@extends('admin.layouts.app')

@section('title', 'Vehicles')
@section('subtitle', 'Manage your fleet of vehicles.')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Vehicles</h1>
            <p class="text-slate-500 mt-1">Manage your fleet of vehicles.</p>
        </div>
        <a href="{{ route('admin.vehicles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Vehicle
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-100 text-green-800 text-sm font-medium flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Type</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Name</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Total Seats</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Price (Indian)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Price (Foreigner)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($vehicles as $vehicle)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $vehicle->type }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $vehicle->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700 text-center">{{ $vehicle->total_seat }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700 text-center">₹{{ number_format($vehicle->price_indian, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700 text-center">₹{{ number_format($vehicle->price_foreigner, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-center space-x-3">
                                <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                                   class="text-blue-600 hover:text-blue-700 hover:underline font-medium">Edit</a>
                                <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this vehicle?')"
                                            class="text-red-600 hover:text-red-700 hover:underline font-medium">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <p class="text-sm font-medium text-slate-900">No vehicles found</p>
                                <p class="text-xs text-slate-400">Get started by adding your first vehicle.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $vehicles->links() }}
    </div>
</div>
@endsection