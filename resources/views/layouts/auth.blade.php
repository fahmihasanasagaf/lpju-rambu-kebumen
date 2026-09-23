<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'LPJU Rambu'))</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
</head>
<body class="auth-page">
    @include('components.asset-marker')
    <main class="auth-shell">
        @include('auth.partials.asset-map')
        <div class="auth-shell__veil" aria-hidden="true"></div>
        <div class="auth-shell__topbar">
            <a href="{{ route('login') }}" class="auth-brand" aria-label="LPJU dan Rambu Kabupaten Kebumen">
                <span class="auth-brand__mark"><img src="{{ asset('storage/foto/logo-kabupaten-kebumen.png') }}" alt="Logo Kabupaten Kebumen" onerror="this.remove(); this.parentElement.textContent='KB';"></span>
                <span><strong>LPJU &amp; Rambu</strong><small>Kabupaten Kebumen</small></span>
            </a>
            <button id="login-map-focus" type="button" class="auth-map-button">Fokus aset</button>
        </div>
        <div class="auth-shell__legend" aria-hidden="true">
            <span><i class="auth-legend-dot auth-legend-dot--lpju"></i>LPJU</span>
            <span><i class="auth-legend-dot auth-legend-dot--rambu"></i>Rambu</span>
        </div>
        <section class="auth-card-wrap">
            <div class="auth-card">
                @yield('content')
            </div>
        </section>
    </main>
    @stack('scripts')
</body>
</html>
