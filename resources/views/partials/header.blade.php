<header class="sticky top-0 z-30 flex w-full min-h-20 items-center justify-between border-b border-border bg-white px-4 shadow-sm sm:px-6 lg:px-8">
    <div class="pl-14 lg:pl-0">
        <p class="text-xs font-semibold uppercase tracking-widest text-teal-600">Panel internal</p>
        <h1 class="text-lg font-semibold text-navy-900">@yield('page-title', 'Dashboard operasional')</h1>
    </div>
    <div class="flex items-center gap-3" x-data="{ menu: false }">
        <div class="hidden text-right sm:block">
            <p class="text-sm font-semibold text-navy-900" x-text="$store.auth.user?.name || 'Petugas'">Petugas</p>
            <p class="text-xs capitalize text-slate-500" x-text="$store.auth.role || 'operator'">operator</p>
        </div>
        <button type="button" @click="menu = !menu" class="flex h-11 w-11 items-center justify-center rounded-full bg-navy-900 text-sm font-semibold text-white" aria-label="Buka menu pengguna" :aria-expanded="menu.toString()">
            <span x-text="($store.auth.user?.name || 'P').slice(0, 1).toUpperCase()">P</span>
        </button>
        <div x-show="menu" @click.outside="menu = false" x-transition class="absolute right-4 top-16 w-48 rounded-xl border border-border bg-white p-2 shadow-lg" x-cloak>
            <button type="button" @click="$store.auth.logout()" class="flex min-h-11 w-full items-center rounded-lg px-3 text-left text-sm text-slate-700 hover:bg-slate-50">Keluar dari panel</button>
        </div>
    </div>
</header>
