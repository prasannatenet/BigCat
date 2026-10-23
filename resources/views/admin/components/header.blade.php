<!-- Header Component -->
<header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-200">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 px-4 lg:px-6 py-3 lg:py-4">
        
        <!-- Left: Title + Mobile Menu Toggle -->
        <div class="flex items-center gap-3 w-full lg:w-auto">
            <button @click="sidebarOpen = true" 
                    class="lg:hidden p-2 text-slate-600 hover:text-slate-900 bg-slate-100 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div class="hidden lg:flex flex-col">
                <h1 class="text-lg font-bold text-slate-900 tracking-tight leading-tight">{{ $title ?? 'Admin Panel' }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle ?? '' }}</p>
            </div>
        </div>

        <!-- Right: User Profile -->
        <div class="ml-auto flex items-center gap-3">
            @if (auth()->check())
                <div class="hidden lg:flex items-center gap-3 px-4 py-2 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-slate-500 capitalize">{{ auth()->user()->email }}</span>
                    </div>
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
                
                <!-- Mobile user profile -->
                <div class="lg:hidden p-2 text-slate-600 hover:text-slate-900 bg-slate-100 rounded-lg">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</header>