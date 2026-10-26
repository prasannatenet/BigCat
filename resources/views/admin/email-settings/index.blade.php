@extends('admin.layouts.app')

@section('title', 'Email Settings')
@section('subtitle', 'Connect the SMTP account used by application email.')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">SMTP Configuration</h1>
            <p class="mt-1 text-sm text-slate-500">Configure outgoing email and send a test message to verify the connection.</p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold {{ $mailSettings ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
            <span class="h-2 w-2 rounded-full {{ $mailSettings ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
            {{ $mailSettings ? 'SMTP configured' : 'Not configured' }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.email-settings.update') }}" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @csrf
        <div class="grid grid-cols-1 gap-x-5 gap-y-5 p-5 sm:grid-cols-2 lg:grid-cols-6 lg:p-8">
            <div class="lg:col-span-3">
                <label for="host" class="mb-2 block text-sm font-semibold text-slate-700">SMTP Host</label>
                <input id="host" name="host" type="text" required maxlength="255" value="{{ old('host', $mailSettings?->host ?? '') }}" placeholder="smtp.example.com" autocomplete="off"
                       class="w-full rounded-xl border border-slate-300 bg-[#faf8f3] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="lg:col-span-1">
                <label for="port" class="mb-2 block text-sm font-semibold text-slate-700">SMTP Port</label>
                <input id="port" name="port" type="number" required min="1" max="65535" value="{{ old('port', $mailSettings?->port ?? 465) }}"
                       class="w-full rounded-xl border border-slate-300 bg-[#faf8f3] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="lg:col-span-2">
                <label for="encryption" class="mb-2 block text-sm font-semibold text-slate-700">Encryption</label>
                <select id="encryption" name="encryption" required
                        class="w-full rounded-xl border border-slate-300 bg-[#faf8f3] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="ssl" @selected(old('encryption', $mailSettings?->encryption ?? 'ssl') === 'ssl')>SSL/TLS (usually port 465)</option>
                    <option value="tls" @selected(old('encryption', $mailSettings?->encryption ?? 'ssl') === 'tls')>STARTTLS (usually port 587)</option>
                    <option value="none" @selected(old('encryption', $mailSettings?->encryption ?? 'ssl') === 'none')>None</option>
                </select>
            </div>
            <div class="lg:col-span-3">
                <label for="username" class="mb-2 block text-sm font-semibold text-slate-700">SMTP Username</label>
                <input id="username" name="username" type="text" maxlength="255" value="{{ old('username', $mailSettings?->username ?? '') }}" autocomplete="username"
                       class="w-full rounded-xl border border-slate-300 bg-[#faf8f3] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="lg:col-span-3">
                <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">SMTP Password</label>
                <input id="password" name="password" type="password" maxlength="2000" autocomplete="new-password" value=""
                       placeholder="{{ $mailSettings?->password ? 'Leave blank to keep current password' : 'Enter SMTP password' }}"
                       class="w-full rounded-xl border border-slate-300 bg-[#faf8f3] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <p class="mt-1.5 text-xs text-slate-500">Stored encrypted. Leave blank to keep the saved password.</p>
            </div>
            <div class="lg:col-span-3">
                <label for="from_email" class="mb-2 block text-sm font-semibold text-slate-700">From Email</label>
                <input id="from_email" name="from_email" type="email" required maxlength="255" value="{{ old('from_email', $mailSettings?->from_email ?? '') }}" placeholder="noreply@example.com" autocomplete="email"
                       class="w-full rounded-xl border border-slate-300 bg-[#faf8f3] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="lg:col-span-3">
                <label for="from_name" class="mb-2 block text-sm font-semibold text-slate-700">From Name</label>
                <input id="from_name" name="from_name" type="text" required maxlength="255" value="{{ old('from_name', $mailSettings?->from_name ?? config('app.name')) }}" placeholder="BigCatt Safari" autocomplete="organization"
                       class="w-full rounded-xl border border-slate-300 bg-[#faf8f3] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="lg:col-span-3">
                <label for="test_recipient_email" class="mb-2 block text-sm font-semibold text-slate-700">Test Recipient Email</label>
                <input id="test_recipient_email" name="test_recipient_email" type="email" maxlength="255" value="{{ old('test_recipient_email', $mailSettings?->test_recipient_email ?? '') }}" placeholder="you@example.com" autocomplete="email"
                       class="w-full rounded-xl border border-slate-300 bg-[#faf8f3] px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <p class="mt-1.5 text-xs text-slate-500">Required when you choose “Save &amp; Send Test Email”.</p>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-8">
            <button type="submit" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                Save Settings
            </button>
            <button type="submit" name="send_test" value="1" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                Save &amp; Send Test Email
            </button>
        </div>
    </form>
</div>
@endsection
