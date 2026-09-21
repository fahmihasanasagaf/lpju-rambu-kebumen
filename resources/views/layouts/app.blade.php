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
    <div class="min-h-screen bg-surface">
        @include('partials.sidebar')
        <div class="min-h-screen lg:pl-[var(--sidebar-width)]">
            @include('partials.header')
            <main class="w-full px-4 py-8 sm:px-6 lg:px-8">
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
</body>
</html>
