@extends('layouts.auth')

@section('title', 'Daftar Operator — '.config('app.name', 'LPJU Rambu'))

@section('content')
<div x-data="registerForm()">
    <div class="auth-form-header">
        <span class="auth-form-mark">LR</span>
        <div>
            <h1 class="auth-form-title">Daftar sebagai Operator</h1>
            <p class="auth-form-description">Buat akun untuk mengelola data aset di panel kerja.</p>
        </div>
    </div>

    <div x-cloak x-show="error" role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm leading-6 text-red-700" x-text="error"></div>
    <form @submit.prevent="submit" novalidate>
        <div class="grid gap-4">
            <div><label for="name" class="field-label">Nama lengkap</label><input id="name" name="name" type="text" x-model="name" autocomplete="name" required class="field-input" placeholder="Nama lengkap"></div>
            <div><label for="email" class="field-label">Email kerja</label><input id="email" name="email" type="email" x-model="email" autocomplete="email" required class="field-input" placeholder="nama@instansi.go.id"></div>
            <div><label for="password" class="field-label">Kata sandi</label><input id="password" name="password" type="password" x-model="password" autocomplete="new-password" minlength="8" required class="field-input" placeholder="Minimal 8 karakter"></div>
            <div><label for="password_confirmation" class="field-label">Konfirmasi kata sandi</label><input id="password_confirmation" name="password_confirmation" type="password" x-model="passwordConfirmation" autocomplete="new-password" required class="field-input" placeholder="Ulangi kata sandi"></div>
        </div>
        <p class="mt-3 text-xs leading-5 text-slate-500">Semua pendaftaran publik otomatis menggunakan role Operator.</p>
        <input type="hidden" name="captcha_token" x-model="captchaToken">
        @if (config('services.recaptcha.site_key'))
            <div class="mt-4 flex justify-center"><div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback="onRecaptchaVerified" data-expired-callback="onRecaptchaExpired" data-error-callback="onRecaptchaError"></div></div>
        @else
            <p class="mt-4 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">reCAPTCHA belum dikonfigurasi.</p>
        @endif
        <button type="submit" :disabled="loading" :aria-busy="loading" class="mt-5 flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-navy-900 px-5 text-sm font-semibold text-white transition-colors hover:bg-navy-800 disabled:cursor-not-allowed disabled:opacity-60"><span x-cloak x-show="loading" class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white" aria-hidden="true"></span><span x-text="loading ? 'Membuat akun...' : 'Daftar sebagai Operator'">Daftar sebagai Operator</span></button>
    </form>
    <p class="mt-5 text-center text-sm text-slate-600">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-teal-700 underline decoration-teal-300 underline-offset-2 hover:text-teal-800">Masuk</a></p>
</div>
@endsection

@push('scripts')
<script>
    window.onRecaptchaVerified = (token) => window.dispatchEvent(new CustomEvent('auth-captcha', { detail: String(token) }));
    window.onRecaptchaExpired = () => window.dispatchEvent(new CustomEvent('auth-captcha', { detail: '' }));
    window.onRecaptchaError = () => window.dispatchEvent(new CustomEvent('auth-captcha', { detail: '' }));

    function registerForm() {
        return {
            name: '', email: '', password: '', passwordConfirmation: '', captchaToken: '', loading: false, error: '',
            init() { window.addEventListener('auth-captcha', (event) => { this.captchaToken = event.detail; }); },
            async submit() {
                this.loading = true; this.error = '';
                try {
                    const siteKey = @js(config('services.recaptcha.site_key'));
                    if (!siteKey) throw new Error('reCAPTCHA belum dikonfigurasi. Isi RECAPTCHA_SITE_KEY terlebih dahulu.');
                    if (!window.grecaptcha) throw new Error('Layanan reCAPTCHA belum siap. Silakan coba lagi.');
                    this.captchaToken = String(window.grecaptcha.getResponse());
                    if (!this.captchaToken) throw new Error('Centang verifikasi reCAPTCHA terlebih dahulu.');
                    const response = await window.axios.post('/api/register', { name: String(this.name), email: String(this.email), password: String(this.password), password_confirmation: String(this.passwordConfirmation), captcha_token: this.captchaToken });
                    Alpine.store('auth').setSession(response.data.user, response.data.token);
                    window.location.href = '{{ route('dashboard') }}';
                } catch (error) {
                    const data = error.response?.data;
                    this.error = data?.errors ? Object.values(data.errors).flat().join(' ') : (data?.message || error.message || 'Registrasi gagal. Silakan coba lagi.');
                    if (window.grecaptcha) window.grecaptcha.reset();
                    this.captchaToken = '';
                } finally { this.loading = false; }
            },
        };
    }
</script>
@endpush
