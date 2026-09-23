<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'LPJU Rambu Kebumen'))</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface">
    <div class="app-shell min-h-screen bg-surface">
        @include('partials.sidebar')
        <div class="app-shell__content min-h-screen lg:pl-[var(--sidebar-width)]">
            @include('partials.header')
            <main class="app-shell__main w-full px-4 py-8 pb-28 sm:px-6 lg:px-8 lg:pb-8">
                <div class="w-full max-w-none">
                    @yield('content')
                </div>
            </main>
            <footer class="w-full border-t border-white/10 bg-navy-900 px-4 py-4 text-center text-xs text-slate-400 sm:px-6">
                <p class="font-medium text-slate-300">LPJU &amp; Rambu Kabupaten Kebumen</p>
                <p class="mt-1">Sistem internal pemantauan aset <span class="text-teal-400">•</span> {{ now()->year }}</p>
            </footer>
        </div>
    </div>
    <nav class="fixed inset-x-0 bottom-0 z-[80] border-t border-border bg-white/95 px-2 pb-[env(safe-area-inset-bottom)] pt-1 shadow-[0_-4px_16px_rgba(15,23,42,0.08)] backdrop-blur lg:hidden" aria-label="Navigasi mobile">
        <div class="grid h-16 grid-cols-4 gap-1">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 flex-col items-center justify-center gap-1 rounded-lg px-1 text-[11px] font-semibold {{ request()->routeIs('dashboard') ? 'text-teal-600' : 'text-slate-500' }}" aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}"><span class="text-lg" aria-hidden="true">⌂</span><span class="truncate">Dashboard</span></a>
            <div x-data="{ open: false }" class="relative flex min-w-0 items-center justify-center"><button type="button" @click="open = !open" :aria-expanded="open.toString()" class="flex min-w-0 flex-col items-center justify-center gap-1 rounded-lg px-1 text-[11px] font-semibold {{ request()->is('assets/lpju*') || request()->is('assets/rambu*') ? 'text-teal-600' : 'text-slate-500' }}"><span class="text-lg" aria-hidden="true">▦</span><span class="truncate">Data Aset</span></button><div x-show="open" x-cloak @click.outside="open = false" class="absolute bottom-16 left-1/2 z-[90] w-36 -translate-x-1/2 rounded-xl border border-border bg-white p-2 text-sm shadow-xl"><a href="{{ route('assets.lpju.index') }}" class="block rounded-lg px-3 py-3 font-semibold text-slate-700 hover:bg-slate-50">Data LPJU</a><a href="{{ route('assets.rambu.index') }}" class="block rounded-lg px-3 py-3 font-semibold text-slate-700 hover:bg-slate-50">Data Rambu</a></div></div>
            <a href="{{ route('assets.map') }}" class="flex min-w-0 flex-col items-center justify-center gap-1 rounded-lg px-1 text-[11px] font-semibold {{ request()->routeIs('assets.map') ? 'text-teal-600' : 'text-slate-500' }}" aria-current="{{ request()->routeIs('assets.map') ? 'page' : 'false' }}"><span class="text-lg" aria-hidden="true">⌖</span><span class="truncate">Peta</span></a>
            <button type="button" @click="$dispatch('open-mobile-menu')" class="flex min-w-0 flex-col items-center justify-center gap-1 rounded-lg px-1 text-[11px] font-semibold text-slate-500"><span class="text-lg" aria-hidden="true">☰</span><span class="truncate">Menu</span></button>
        </div>
    </nav>
</body>
</html>
