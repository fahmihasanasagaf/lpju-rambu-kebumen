@extends('layouts.auth')

@section('title', 'Masuk — '.config('app.name', 'LPJU Rambu'))

@section('content')
<div x-data="loginForm()">
    <div class="auth-form-header">
        <span class="auth-form-mark">LR</span>
        <div>
            <h1 class="auth-form-title">Masuk ke panel kerja</h1>
            <p class="auth-form-description">Kelola aset penerangan dan rambu Kabupaten Kebumen.</p>
        </div>
    </div>

    <div x-cloak x-show="error" role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm leading-6 text-red-700" x-text="error"></div>
    <form @submit.prevent="submit" novalidate>
        <div class="grid gap-4">
            <div>
                <label for="email" class="field-label">Email kerja</label>
                <input id="email" name="email" type="email" x-model="email" autocomplete="email" required class="field-input" placeholder="nama@instansi.go.id">
            </div>
            <div>
                <label for="password" class="field-label">Kata sandi</label>
                <input id="password" name="password" type="password" x-model="password" autocomplete="current-password" required class="field-input" placeholder="Masukkan kata sandi">
            </div>
        </div>
        <input type="hidden" name="captcha_token" x-model="captchaToken">
        @if (config('services.recaptcha.site_key'))
            <div class="mt-4 flex justify-center"><div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback="onRecaptchaVerified" data-expired-callback="onRecaptchaExpired" data-error-callback="onRecaptchaError"></div></div>
        @else
            <p class="mt-4 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">reCAPTCHA belum dikonfigurasi.</p>
        @endif
        <button type="submit" :disabled="loading" :aria-busy="loading" class="mt-5 flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-navy-900 px-5 text-sm font-semibold text-white transition-colors hover:bg-navy-800 disabled:cursor-not-allowed disabled:opacity-60"><span x-cloak x-show="loading" class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white" aria-hidden="true"></span><span x-text="loading ? 'Memverifikasi akses...' : 'Masuk ke dashboard'">Masuk ke dashboard</span></button>
    </form>
    <p class="mt-5 text-center text-sm text-slate-600">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-teal-700 underline decoration-teal-300 underline-offset-2 hover:text-teal-800">Daftar sebagai Operator</a></p>
    <p class="mt-4 text-center text-[11px] leading-5 text-slate-500">Dilindungi reCAPTCHA. Google <a class="underline" href="https://policies.google.com/privacy" target="_blank" rel="noreferrer">Privacy Policy</a> dan <a class="underline" href="https://policies.google.com/terms" target="_blank" rel="noreferrer">Terms</a> berlaku.</p>
</div>
@endsection

@push('scripts')
<script>
    window.onRecaptchaVerified = (token) => window.dispatchEvent(new CustomEvent('auth-captcha', { detail: String(token) }));
    window.onRecaptchaExpired = () => window.dispatchEvent(new CustomEvent('auth-captcha', { detail: '' }));
    window.onRecaptchaError = () => window.dispatchEvent(new CustomEvent('auth-captcha', { detail: '' }));

    function loginForm() {
        return {
            email: '', password: '', captchaToken: '', loading: false, error: '',
            init() { window.addEventListener('auth-captcha', (event) => { this.captchaToken = event.detail; }); },
            async submit() {
                this.loading = true; this.error = '';
                try {
                    const siteKey = @js(config('services.recaptcha.site_key'));
                    if (!siteKey) throw new Error('reCAPTCHA belum dikonfigurasi. Isi RECAPTCHA_SITE_KEY terlebih dahulu.');
                    if (!window.grecaptcha) throw new Error('Layanan reCAPTCHA belum siap. Silakan coba lagi.');
                    this.captchaToken = String(window.grecaptcha.getResponse());
                    if (!this.captchaToken) throw new Error('Centang verifikasi reCAPTCHA terlebih dahulu.');
                    const response = await window.axios.post('/api/login', { email: String(this.email), password: String(this.password), captcha_token: this.captchaToken });
                    Alpine.store('auth').setSession(response.data.user, response.data.token);
                    window.location.href = '{{ route('dashboard') }}';
                } catch (error) {
                    const data = error.response?.data;
                    this.error = data?.errors ? Object.values(data.errors).flat().join(' ') : (data?.message || error.message || 'Login gagal. Silakan coba lagi.');
                    if (window.grecaptcha) window.grecaptcha.reset();
                    this.captchaToken = '';
                } finally { this.loading = false; }
            },
        };
    }
</script>
@endpush
