<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — {{ config('app.name', 'LPJU Rambu') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
</head>
<body class="min-h-screen bg-navy-950 text-slate-900" x-data="loginForm()">
    @include('components.asset-marker')
    <main class="grid min-h-screen lg:grid-cols-[minmax(0,0.9fr)_minmax(420px,1.1fr)]">
        <section class="relative hidden overflow-hidden bg-navy-900 px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between xl:px-20" aria-label="Peta aset Kebumen">
            <div id="login-asset-map" class="absolute inset-0 min-h-full bg-[#dbe7e5]"></div>
            <div class="pointer-events-none absolute inset-0 bg-navy-950/10"></div>
            <div class="pointer-events-auto absolute right-4 top-4 z-[900] flex gap-2"><button id="login-map-focus" type="button" class="rounded-lg border border-white/50 bg-navy-900/80 px-3 py-2 text-xs font-semibold text-white shadow-lg backdrop-blur-sm">Fokus aset</button></div>
            <div class="pointer-events-none relative z-10 flex items-center gap-3"><div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-500 text-lg font-bold text-navy-950">LR</div><div><p class="font-semibold">LPJU &amp; Rambu Kabupaten Kebumen</p><p class="text-sm text-slate-200">Pantau aset secara terukur</p></div></div>
            <div class="pointer-events-auto absolute bottom-16 left-6 z-[900] max-w-md rounded-2xl border border-white/20 bg-navy-950/55 p-5 backdrop-blur-sm"><p class="text-lg font-semibold">Peta aset Kebumen</p><p class="mt-1 text-sm leading-6 text-slate-200">Pantau aset penerangan dan rambu secara terukur.</p><div class="mt-4 flex gap-4 text-xs text-white"><span class="flex items-center gap-2"><i class="h-2.5 w-2.5 rounded-full bg-teal-400"></i>LPJU</span><span class="flex items-center gap-2"><i class="h-2.5 w-2.5 rounded-full bg-amber-400"></i>Rambu</span></div></div>
            <div class="pointer-events-none relative z-10 text-xs text-slate-200">Peta 2D dengan marker aset bergaya isometrik.</div>
        </section>
        <section class="flex items-center justify-center bg-surface px-5 py-10 sm:px-8">
            <div class="w-full max-w-md" x-cloak>
                <div class="mb-8 lg:hidden"><div class="flex items-center gap-3"><div class="flex h-11 w-11 items-center justify-center rounded-xl bg-navy-900 text-sm font-bold text-teal-400">LR</div><div><p class="font-semibold text-navy-900">LPJU &amp; Rambu</p><p class="text-xs text-slate-500">Kabupaten Kebumen</p></div></div></div>
                <div class="mb-8"><p class="text-sm font-semibold uppercase tracking-widest text-teal-600">Selamat datang</p><h2 class="mt-2 text-3xl font-semibold tracking-tight text-navy-900">Masuk ke panel kerja</h2><p class="mt-2 text-sm leading-6 text-slate-600">Gunakan akun Admin atau Operator yang telah terdaftar.</p></div>
                <div class="mb-7 grid grid-cols-2 rounded-xl bg-slate-200/70 p-1" role="tablist" aria-label="Jenis akses">
                    <button type="button" @click="selectedRole = 'admin'" :class="selectedRole === 'admin' ? 'bg-white text-navy-900 shadow-sm' : 'text-slate-600'" class="min-h-11 rounded-lg px-4 text-sm font-semibold transition-colors" role="tab" :aria-selected="selectedRole === 'admin'">Admin</button>
                    <button type="button" @click="selectedRole = 'operator'" :class="selectedRole === 'operator' ? 'bg-white text-navy-900 shadow-sm' : 'text-slate-600'" class="min-h-11 rounded-lg px-4 text-sm font-semibold transition-colors" role="tab" :aria-selected="selectedRole === 'operator'">Operator</button>
                </div>
                <div x-show="error" role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm leading-6 text-red-700" x-text="error"></div>
                <form @submit.prevent="submit" novalidate>
                    <div class="space-y-5">
                        <div><label for="email" class="mb-2 block text-sm font-semibold text-navy-900">Email kerja</label><input id="email" name="email" type="email" x-model="email" autocomplete="email" required class="h-12 w-full rounded-xl border border-border bg-white px-4 text-sm text-navy-900 placeholder:text-slate-400" placeholder="nama@instansi.go.id"></div>
                        <div><label for="password" class="mb-2 block text-sm font-semibold text-navy-900">Kata sandi</label><input id="password" name="password" type="password" x-model="password" autocomplete="current-password" required class="h-12 w-full rounded-xl border border-border bg-white px-4 text-sm text-navy-900 placeholder:text-slate-400" placeholder="Masukkan kata sandi"></div>
                    </div>
                    <input type="hidden" name="captcha_token" x-model="captchaToken">
                    <div class="mt-5 flex justify-center"><div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback="onRecaptchaVerified" data-expired-callback="onRecaptchaExpired" data-error-callback="onRecaptchaError"></div></div>
                    <button type="submit" :disabled="loading" class="mt-7 flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-navy-900 px-5 text-sm font-semibold text-white transition-colors hover:bg-navy-800 disabled:cursor-not-allowed disabled:opacity-60"><span x-show="loading" class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white" aria-hidden="true"></span><span x-text="loading ? 'Memverifikasi akses...' : 'Masuk ke dashboard'">Masuk ke dashboard</span></button>
                </form>
                <p class="mt-6 text-center text-xs leading-5 text-slate-500">Dilindungi reCAPTCHA. Google <a class="underline" href="https://policies.google.com/privacy" target="_blank" rel="noreferrer">Privacy Policy</a> dan <a class="underline" href="https://policies.google.com/terms" target="_blank" rel="noreferrer">Terms</a> berlaku.</p>
            </div>
        </section>
    </main>
    <script>
        (() => {
            const bootMap = () => {
                if (!window.Leaflet) return;
                const node = document.getElementById('login-asset-map');
                if (!node || node.dataset.ready) return;
                node.dataset.ready = 'true';
                const map = window.Leaflet.map(node, { center: [-7.6786, 109.6565], zoom: 11, minZoom: 9, maxZoom: 18, scrollWheelZoom: false });
                window.Leaflet.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
                const markers = [];
                const normalize = (response) => Array.isArray(response?.data) ? response.data : (Array.isArray(response) ? response : (Array.isArray(response?.data?.data) ? response.data.data : []));
                const validAssets = [];
                const showAssets = (items, type, group) => (items || []).forEach((item, index) => {
                    const asset = { ...item, type, kode: `${type === 'lpju' ? 'LPJU' : 'RAMBU'}-${String(item.id ?? index + 1).padStart(4, '0')}` };
                    const lat = Number.parseFloat(asset.latitude), lng = Number.parseFloat(asset.longitude);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) return;
                    validAssets.push(asset);
                    const marker = window.Leaflet.marker([lat, lng], { icon: window.assetMarkerIcon(asset), title: asset.kode, alt: `${asset.type} ${asset.kode}` });
                    marker.bindTooltip(asset.kode, { direction: 'top', offset: [0, -58] });
                    marker.bindPopup(`<strong>${asset.kode}</strong><br>${asset.alamat || '-'}<br>Status: ${asset.status || '-'}`);
                    marker.addTo(group);
                    markers.push(marker);
                });
                const lpju = window.Leaflet.layerGroup().addTo(map), rambu = window.Leaflet.layerGroup().addTo(map);
                const focusButton = document.getElementById('login-map-focus');
                const focusAssets = () => {
                    if (!validAssets.length) return console.warn('[login-map] Belum ada aset dengan koordinat valid');
                    map.fitBounds(window.Leaflet.latLngBounds(validAssets.map(asset => [Number.parseFloat(asset.latitude), Number.parseFloat(asset.longitude)])), { padding: [28, 28], maxZoom: 14 });
                };
                focusButton?.addEventListener('click', focusAssets);
                Promise.all([window.axios.get('/api/lpju'), window.axios.get('/api/rambu')]).then(([a, b]) => {
                    const lpjuData = normalize(a), rambuData = normalize(b);
                    console.log('[login-map] lpju response', lpjuData);
                    console.log('[login-map] rambu response', rambuData);
                    showAssets(lpjuData, 'lpju', lpju);
                    showAssets(rambuData, 'rambu', rambu);
                    console.log('[login-map] valid coordinates', validAssets);
                    console.log('[login-map] markers rendered', markers.length);
                    focusAssets();
                }).catch((error) => console.warn('[login-map] marker data unavailable', error)).finally(() => { map.invalidateSize(); setTimeout(() => map.invalidateSize(), 100); });
                setTimeout(() => map.invalidateSize(), 100);
            };
            document.addEventListener('DOMContentLoaded', bootMap);
            window.addEventListener('load', bootMap);
        })();
    </script>
    <script>
        window.onRecaptchaVerified = (token) => {
            const root = document.querySelector('[x-data^="loginForm"]');
            if (root?._x_dataStack?.[0]) root._x_dataStack[0].captchaToken = String(token);
        };
        window.onRecaptchaExpired = () => {
            const root = document.querySelector('[x-data^="loginForm"]');
            if (root?._x_dataStack?.[0]) root._x_dataStack[0].captchaToken = '';
        };
        window.onRecaptchaError = () => console.warn('[recaptcha] widget error; verify allowed domains and matching v2 keys');

        function loginForm() {
            return {
                email: '', password: '', captchaToken: '', selectedRole: 'operator', loading: false, error: '',
                async submit() {
                    this.loading = true; this.error = '';
                    try {
                        const siteKey = @js(config('services.recaptcha.site_key'));
                        if (!siteKey) throw new Error('reCAPTCHA belum dikonfigurasi. Isi RECAPTCHA_SITE_KEY terlebih dahulu.');
                        if (!window.grecaptcha) throw new Error('Layanan reCAPTCHA belum siap. Silakan coba lagi.');
                        this.captchaToken = String(window.grecaptcha.getResponse());
                        if (!this.captchaToken) throw new Error('Centang verifikasi reCAPTCHA terlebih dahulu.');
                        const response = await window.axios.post('/api/login', {
                            email: String(this.email),
                            password: String(this.password),
                            captcha_token: String(this.captchaToken),
                        });
                        Alpine.store('auth').setSession(response.data.user, response.data.token);
                        window.location.href = '{{ route('dashboard') }}';
                    } catch (error) {
                        const data = error.response?.data;
                        this.error = data?.errors ? Object.values(data.errors).flat().join(' ') : (data?.message || error.message || 'Login gagal. Silakan coba lagi.');
                        if (window.grecaptcha) window.grecaptcha.reset();
                        this.captchaToken = '';
                    } finally { this.loading = false; }
                }
            };
        }
    </script>
</body>
</html>
