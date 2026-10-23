@extends('admin.layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-10 lg:mt-16">
        <div class="bg-white p-8 rounded-lg shadow-xl border border-gray-200">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Admin Login</h2>
                <p class="text-sm text-gray-500 mt-2">Sign in to your admin account</p>
            </div>

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors"
                        placeholder="admin@example.com">
                </div>

                <div class="relative">

                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    
<div class="relative flex items-center">
    <input
        type="password"
        name="password"
        id="password"
        required
        autocomplete="current-password"
        class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none transition-colors"
        placeholder="••••••••"
    >

    <button
        type="button"
        id="togglePassword"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none flex items-center justify-center"
        aria-label="Toggle password visibility"
    >
        <svg
            id="eyeOpen"
            class="w-5 h-5 hidden"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            viewBox="0 0 24 24"
        >
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>

        <svg
            id="eyeClosed"
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            viewBox="0 0 24 24"
        >
            <path d="M17.5 12c0-1.1-0.9-2-2-2s-2 0.9-2 2 0.9 2 2 2 2-0.9 2-2z"/>
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <path d="m5.5 18.5 11-11"/>
        </svg>
    </button>
</div>

                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="text-blue-600 hover:text-blue-500 hover:underline">Forgot password?</a>
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Sign in
                </button>
            </form>

            <div class="mt-8 p-4 rounded-lg bg-blue-50 border border-blue-100 text-blue-800 text-sm text-center">
                Demo credentials:<br>
                <strong class="block">admin@example.com</strong> / <strong class="block">password</strong>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var toggleBtn = document.getElementById('togglePassword');
            var passwordInput = document.getElementById('password');
            var eyeOpen = document.getElementById('eyeOpen');
            var eyeClosed = document.getElementById('eyeClosed');
            var hideTimer = null;

            function showPassword() {
                passwordInput.type = 'text';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');

                // Auto-hide after 6 seconds
                if (hideTimer) clearTimeout(hideTimer);
                hideTimer = setTimeout(hidePassword, 6000);
            }

            function hidePassword() {
                passwordInput.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
                if (hideTimer) clearTimeout(hideTimer);
            }

            toggleBtn.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    showPassword();
                } else {
                    hidePassword();
                }
            });
        })();
    </script>
@endsection