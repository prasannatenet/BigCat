<!-- Sidebar Component -->
@php
    $isAdmin = auth()->check() && auth()->user()->is_admin;
@endphp

@if ($isAdmin)
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full transform flex-col border-r border-slate-800 bg-slate-900 text-slate-200 shadow-xl transition-all duration-300 lg:translate-x-0">
    
    <!-- Brand -->
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
            </div>
            <span class="text-lg font-bold text-white tracking-tight">BigCatt<span class="text-blue-500">Admin</span></span>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden p-2 text-slate-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl bg-blue-600/10 text-blue-400 border border-blue-500/20 hover:bg-blue-600/20 hover:text-blue-300 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1H5z"></path>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('admin.vehicles.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Vehicles
        </a>
        <a href="{{ route('admin.national-parks.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1H5z"></path>
            </svg>
            National Parks
        </a>
        <a href="{{ route('admin.slots.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('admin.slots.*') ? 'bg-blue-600/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Slot Booking
        </a>
    </nav>

    <!-- Footer / Logout -->
    <div class="p-6 border-t border-slate-800 bg-slate-950">
        <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
        <button onclick="document.getElementById('admin-logout-form').submit();"
                class="flex w-full items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all border border-red-500/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Sign Out
        </button>
    </div>
</aside>
@endif