<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? 'Admin Panel' }} &bull; {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900 antialiased">

    @php
        $isAdmin = auth()->check() && auth()->user()->is_admin;
    @endphp

    <div x-data="{ sidebarOpen: false }">
        <div x-show="sidebarOpen" x-transition.opacity x-cloak
             class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
             @click="sidebarOpen = false"></div>

        @include('admin.components.sidebar')

        <div class="flex h-screen flex-col lg:pl-64">
            @include('admin.components.header', ['title' => $title ?? 'Admin Panel', 'subtitle' => $subtitle ?? ''])

            <main class="relative flex-1 overflow-hidden">
                <div class="h-full overflow-y-auto p-4 lg:p-6 xl:p-8">
                    @if (session('status'))
                        <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-100 bg-green-50 p-4 text-sm font-medium text-green-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-sm font-medium text-red-800">
                            <h3 class="mb-2 font-bold">Please fix the following errors:</h3>
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>