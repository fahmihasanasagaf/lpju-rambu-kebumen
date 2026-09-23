<div x-data="{ open: false }" @keydown.escape.window="open = false" @open-mobile-menu.window="open = true">
<aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-[70] flex w-72 -translate-x-full flex-col bg-navy-900 text-white transition-transform duration-200 lg:translate-x-0" :class="{ 'translate-x-0': open }" aria-label="Navigasi utama">
    <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6">
        <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl bg-teal-500 text-lg font-bold text-navy-950">
            <img src="{{ asset('storage/foto/logo-kabupaten-kebumen.png') }}" alt="Logo Kabupaten Kebumen" class="h-full w-full object-contain p-1" onerror="this.remove(); this.parentElement.textContent='KB';">
        </div>
        <div>
            <p class="font-semibold tracking-tight">LPJU &amp; Rambu</p>
            <p class="text-xs text-slate-400">Kabupaten Kebumen</p>
        </div>
    </div>
    <div class="border-b border-white/10 px-6 py-5">
        <p class="text-xs uppercase tracking-wider text-slate-400">Ruang kerja</p>
        <div class="mt-3 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-navy-800 font-semibold text-teal-400" x-text="($store.auth.user?.name || 'Petugas').slice(0, 1).toUpperCase()">P</div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium" x-text="$store.auth.user?.name || 'Petugas lapangan'">Petugas lapangan</p>
                <span class="mt-1 inline-flex rounded-full bg-teal-500/15 px-2 py-0.5 text-[11px] font-medium uppercase tracking-wide text-teal-300" x-text="$store.auth.role || 'operator'">operator</span>
            </div>
        </div>
    </div>
    <nav class="flex-1 space-y-1 px-3 py-5">
        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-widest text-slate-500">Operasional</p>
        <a href="{{ route('dashboard') }}" class="flex min-h-11 items-center gap-3 rounded-lg border-l-4 border-transparent px-3 text-sm text-slate-300 transition-colors hover:bg-navy-800 hover:text-white">
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z" /></svg>
            Dashboard
        </a>
        <div x-data="{ open: {{ request()->is('assets/*') ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="flex min-h-11 w-full items-center justify-between gap-3 rounded-lg border-l-4 border-transparent px-3 text-sm text-slate-300 hover:bg-navy-800 hover:text-white">
                <span class="flex items-center gap-3"><span aria-hidden="true">▦</span>Data Aset</span><span :class="open ? 'rotate-180' : ''" class="transition-transform">⌄</span>
            </button>
            <div x-show="open" x-cloak class="ml-8 space-y-1 border-l border-white/10 py-1 pl-3">
                <a href="{{ route('assets.lpju.index') }}" class="block rounded px-3 py-2 text-sm text-slate-300 hover:bg-navy-800 hover:text-white">LPJU</a>
                <a href="{{ route('assets.rambu.index') }}" class="block rounded px-3 py-2 text-sm text-slate-300 hover:bg-navy-800 hover:text-white">Rambu</a>
            </div>
        </div>
        <a x-show="$store.auth.role === 'admin'" href="{{ route('users.index') }}" class="flex min-h-11 items-center gap-3 rounded-lg border-l-4 border-transparent px-3 text-sm text-slate-300 transition-colors hover:bg-navy-800 hover:text-white">
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-8a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-3v6m3-3h-6" /></svg>
            Pengguna
        </a>
        <a x-show="['admin', 'operator'].includes($store.auth.role)" href="{{ route('complaints.index') }}" class="flex min-h-11 items-center gap-3 rounded-lg border-l-4 border-transparent px-3 text-sm text-slate-300 transition-colors hover:bg-navy-800 hover:text-white"><span aria-hidden="true">!</span>Aduan</a>
        <a href="{{ route('history.index') }}" class="flex min-h-11 items-center gap-3 rounded-lg border-l-4 border-transparent px-3 text-sm text-slate-300 transition-colors hover:bg-navy-800 hover:text-white">
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            Histori perubahan
        </a>
    </nav>
    <div class="border-t border-white/10 p-3">
        <button type="button" @click="$store.auth.logout()" class="flex min-h-11 w-full items-center gap-3 rounded-lg px-3 text-sm text-slate-300 transition-colors hover:bg-red-500/10 hover:text-red-200">
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 12h9m0 0-3-3m3 3-3 3" /></svg>
            Keluar
        </button>
    </div>
</aside>
<div x-show="open" x-cloak x-transition.opacity @click="open = false" @keydown.escape.window="open = false" class="fixed inset-0 z-[60] bg-navy-950/60 lg:hidden" aria-hidden="true"></div>
<div class="lg:hidden">
    <button type="button" @click="open = !open" class="fixed left-4 top-4 z-[80] flex h-11 w-11 items-center justify-center rounded-lg bg-navy-900 text-white shadow-lg" aria-label="Buka navigasi" :aria-expanded="open.toString()" aria-controls="mobile-sidebar">
        <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
    </button>
</div>
