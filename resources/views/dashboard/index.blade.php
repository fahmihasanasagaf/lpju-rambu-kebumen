@extends('layouts.app')

@section('title', 'Dashboard — LPJU & Rambu')
@section('page-title', 'Dashboard operasional')

@section('content')
@include('components.asset-marker')
<div x-data="dashboardSummary()" x-init="init()" x-cloak>
    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="text-sm text-slate-600">Ringkasan pemantauan hari ini</p><h2 class="mt-1 text-2xl font-semibold tracking-tight text-navy-900 sm:text-3xl">Selamat bekerja, <span x-text="$store.auth.user?.name || 'Petugas'">Petugas</span></h2></div>
        <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto"><a href="{{ route('assets.map') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-navy-900 px-4 text-sm font-semibold text-white transition-colors hover:bg-navy-800"><svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Zm6-3v15m6-12v15" /></svg> Buka peta aset</a><div class="inline-flex items-center gap-2 rounded-full border border-border bg-white px-3 py-2 text-xs font-medium text-slate-600"><span class="h-2 w-2 rounded-full bg-teal-500"></span> Sistem aktif <span class="text-slate-300">•</span> {{ now()->format('d M Y') }}</div></div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([['label' => 'Total LPJU', 'value' => '1.248', 'meta' => '+8 aset bulan ini', 'color' => 'teal'], ['label' => 'Total Rambu', 'value' => '836', 'meta' => '+12 aset bulan ini', 'color' => 'yellow'], ['label' => 'Aduan masuk', 'value' => '24', 'meta' => '7 perlu ditindak', 'color' => 'orange'], ['label' => 'Aduan selesai', 'value' => '91%', 'meta' => '+4% dibanding bulan lalu', 'color' => 'blue']] as $stat)
            <article class="rounded-2xl border border-border bg-white p-5 shadow-sm"><div class="flex items-start justify-between"><p class="text-sm font-medium text-slate-600">{{ $stat['label'] }}</p><span class="h-3 w-3 rounded-full bg-{{ $stat['color'] }}-400"></span></div><p class="mt-4 text-3xl font-semibold tracking-tight text-navy-900"><span x-text="stats['{{ $stat['label'] }}']">{{ $stat['value'] }}</span></p><p class="mt-2 text-xs text-slate-500">{{ $stat['meta'] }}</p></article>
        @endforeach
    </div>
    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(300px,0.55fr)]">
        <section x-data="dashboardComplaints()" x-init="init()" class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm"><div class="flex items-center justify-between border-b border-border px-5 py-4"><div><h3 class="font-semibold text-navy-900">Aduan terbaru</h3><p class="mt-1 text-xs text-slate-500" x-text="loading ? 'Memuat aduan...' : `${items.length} laporan terbaru`">Prioritas penanganan lapangan</p></div><a href="{{ route('complaints.index') }}" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-teal-700 hover:bg-teal-50">Lihat semua</a></div><div class="overflow-x-auto"><table class="w-full min-w-[600px] text-left text-sm"><caption class="sr-only">Daftar aduan terbaru</caption><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 font-semibold">Aset</th><th class="px-5 py-3 font-semibold">Wilayah</th><th class="px-5 py-3 font-semibold">Tanggal</th><th class="px-5 py-3 font-semibold">Status</th></tr></thead><tbody class="divide-y divide-border"><template x-for="item in items" :key="item.id"><tr class="hover:bg-slate-50"><td class="px-5 py-4 font-semibold text-navy-900"><a :href="`{{ url("/complaints") }}/${item.id}`" class="text-teal-700 hover:underline" x-text="code(item)"></a></td><td class="px-5 py-4 text-slate-600" x-text="item.alamat_kejadian || item.aset?.alamat || '-'"></td><td class="px-5 py-4 text-slate-600" x-text="item.tanggal_aduan || '-'"></td><td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium" :class="statusClass(item.status_aduan)" x-text="item.status_aduan"></span></td></tr></template><tr x-show="!loading && !items.length"><td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada aduan.</td></tr></tbody></table></div></section>
        <section x-data="dashboardMapPreview()" x-init="init()" class="relative overflow-hidden rounded-2xl border border-border bg-white shadow-sm"><div class="flex items-start justify-between border-b border-border px-5 py-4"><div><p class="text-xs font-semibold uppercase tracking-widest text-teal-600">PETA ASET</p><h3 class="mt-1 text-xl font-semibold text-navy-900">Peta aset Kebumen</h3><p class="mt-1 text-sm text-slate-600">Pantau lokasi LPJU dan Rambu secara langsung.</p></div><a href="{{ route('assets.map') }}" class="inline-flex min-h-10 items-center rounded-lg bg-navy-900 px-3 text-xs font-semibold text-white hover:bg-navy-800">Buka peta lengkap</a></div><div class="dashboard-map-shell relative min-h-[300px]"><div id="dashboard-asset-map" class="absolute inset-0"></div><div class="pointer-events-none absolute bottom-3 left-3 z-[500] rounded-lg bg-white/95 px-3 py-2 text-xs text-slate-700 shadow"><span class="mr-3"><i class="mr-1 inline-block h-2.5 w-2.5 rounded-full bg-teal-500"></i>LPJU <strong x-text="counts.lpju">0</strong></span><span><i class="mr-1 inline-block h-2.5 w-2.5 rounded-full bg-amber-500"></i>Rambu <strong x-text="counts.rambu">0</strong></span></div><div x-cloak x-show="mapLoading" class="dashboard-map-loading absolute inset-0 z-[600] flex items-center justify-center gap-2 bg-white/75 text-sm text-slate-600"><span class="dashboard-map-spinner" aria-hidden="true"></span><span>Memuat peta...</span></div><div x-cloak x-show="assetLayerError" class="absolute inset-x-4 top-4 z-[600] rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800" x-text="assetLayerError"></div><div x-cloak x-show="tileError" class="absolute inset-x-4 bottom-4 z-[600] rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700" x-text="tileError"></div><div x-cloak x-show="mapError && !map" class="absolute inset-x-4 top-4 z-[600] rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700" x-text="mapError"></div></div></section>
    </div>
</div>
<script>
const dashboardApiBase = @json(url('/api'));

function dashboardSummary() {
    return {
        stats: { 'Total LPJU': '—', 'Total Rambu': '—', 'Aduan masuk': '—', 'Aduan selesai': '—' },
        async init() {
            const list = (response) => {
                let value = response?.data ?? response;
                while (value && !Array.isArray(value) && value.data !== undefined) value = value.data;
                return Array.isArray(value) ? value : [];
            };
            const summary = (response) => {
                let value = response?.data ?? response;
                while (value && value.data && typeof value.data === 'object' && !Array.isArray(value.data)) value = value.data;
                return value || {};
            };
            await Promise.allSettled([
                window.axios.get(`${dashboardApiBase}/lpju`).then((response) => { this.stats['Total LPJU'] = list(response).length.toLocaleString('id-ID'); }),
                window.axios.get(`${dashboardApiBase}/rambu`).then((response) => { this.stats['Total Rambu'] = list(response).length.toLocaleString('id-ID'); }),
                window.axios.get(`${dashboardApiBase}/aduan/summary`).then((response) => {
                    const data = summary(response);
                    if (Number.isFinite(Number(data.total))) this.stats['Aduan masuk'] = Number(data.total).toLocaleString('id-ID');
                    if (Number.isFinite(Number(data.persentase_selesai))) this.stats['Aduan selesai'] = `${Number(data.persentase_selesai)}%`;
                }),
            ]);
        },
    };
}

function dashboardComplaints() {
    return {
        items: [], loading: true, error: '',
        async init() {
            try {
                const response = await window.axios.get(`${dashboardApiBase}/aduan`);
                let body = response?.data ?? response;
                while (body && !Array.isArray(body) && body.data !== undefined) body = body.data;
                this.items = (Array.isArray(body) ? body : []).sort((a, b) => String(b.tanggal_aduan || '').localeCompare(String(a.tanggal_aduan || ''))).slice(0, 5);
            } catch (error) { this.error = 'Gagal memuat data aduan.'; }
            finally { this.loading = false; }
        },
        code(item) { return `ADUAN-${String(item.id).padStart(6, '0')}`; },
        statusClass(status) { return status === 'selesai' ? 'bg-teal-100 text-teal-800' : status === 'diproses' ? 'bg-yellow-100 text-yellow-800' : status === 'baru' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700'; },
    };
}

function dashboardMapPreview() {
    return {
        map: null, mapLoading: true, mapError: '', assetLayerError: '', tileError: '', counts: { lpju: 0, rambu: 0 },
        async init() {
            const startedAt = performance.now();
            try {
                await this.$nextTick();
                const container = document.getElementById('dashboard-asset-map');
                if (!container || !window.Leaflet) throw new Error('Map container or Leaflet unavailable.');
                this.map = window.Leaflet.map(container, { center: [-7.6786, 109.6565], zoom: 10, minZoom: 9, maxZoom: 14, scrollWheelZoom: false, zoomControl: false });
                window.Leaflet.control.zoom({ position: 'bottomright' }).addTo(this.map); this.mapError = '';
                const tiles = window.Leaflet.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(this.map);
                tiles.on('tileload', () => { this.tileError = ''; this.mapError = ''; }); tiles.on('tileerror', () => { this.tileError = 'Peta dasar gagal dimuat.'; });
                const normalize = (response) => { let value = response?.data ?? response; while (value && !Array.isArray(value) && value.data !== undefined) value = value.data; return Array.isArray(value) ? value : []; };
                const results = await Promise.allSettled([window.axios.get(`${dashboardApiBase}/lpju`), window.axios.get(`${dashboardApiBase}/rambu`)]);
                let loaded = 0;
                for (const [result, type] of [[results[0], 'lpju'], [results[1], 'rambu']]) {
                    if (result.status === 'rejected') continue;
                    const assets = normalize(result.value); this.counts[type] = assets.length; loaded++;
                    assets.forEach((asset) => { const lat = Number.parseFloat(asset.latitude), lng = Number.parseFloat(asset.longitude); if (!Number.isFinite(lat) || !Number.isFinite(lng)) return; const item = { ...asset, type, kode: `${type === 'lpju' ? 'LPJU' : 'RAMBU'}-${String(asset.id).padStart(4, '0')}` }; try { window.Leaflet.marker([lat, lng], { icon: window.assetMarkerIcon(item), title: item.kode }).bindTooltip(item.kode, { direction: 'top' }).addTo(this.map); } catch (error) { this.assetLayerError = 'Sebaran aset belum dapat dimuat.'; } });
                }
                if (loaded < 2) this.assetLayerError = 'Sebaran aset belum dapat dimuat.';
            } catch (error) { this.mapError = 'Peta tidak dapat dibuat.'; }
            finally { const remaining = Math.max(0, 600 - (performance.now() - startedAt)); setTimeout(() => { this.mapLoading = false; if (this.map) this.map.invalidateSize(); }, remaining); }
        },
    };
}
</script>
@endsection
