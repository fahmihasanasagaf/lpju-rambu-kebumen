@extends('layouts.app')

@section('title', 'Peta Aset — LPJU & Rambu')
@section('page-title', 'Peta aset Kebumen')

@section('content')
@include('components.asset-marker')
<div x-data="assetMap()" x-init="init()" class="space-y-4" x-cloak>
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm text-slate-600">Sebaran aset LPJU dan Rambu lintas kecamatan</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-navy-900 sm:text-3xl">Peta aset Kebumen</h2>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-teal-500"></span> LPJU</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span> Rambu</span>
            <span class="text-slate-300">|</span>
            <span x-text="`${filtered.length} aset ditampilkan`">0 aset ditampilkan</span>
        </div>
    </div>

    <section class="rounded-2xl border border-border bg-white p-4 shadow-sm">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
            <div class="xl:col-span-2">
                <label for="filter-search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari kode / alamat</label>
                <input id="filter-search" type="search" x-model="search" placeholder="LPJU-0001 atau nama jalan" class="h-11 w-full rounded-lg border border-border px-3 text-sm text-navy-900 placeholder:text-slate-400">
            </div>
            <div>
                <label for="filter-type" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis aset</label>
                <select id="filter-type" x-model="filters.type" class="h-11 w-full rounded-lg border border-border bg-white px-3 text-sm text-navy-900">
                    <option value="">Semua</option><option value="lpju">LPJU</option><option value="rambu">Rambu</option>
                </select>
            </div>
            <div>
                <label for="filter-kecamatan" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Kecamatan</label>
                <select id="filter-kecamatan" x-model="filters.kecamatan" class="h-11 w-full rounded-lg border border-border bg-white px-3 text-sm text-navy-900">
                    <option value="">Semua</option>
                    <template x-for="kec in kecamatanList" :key="kec.kode"><option :value="kec.kode" x-text="kec.kecamatan"></option></template>
                </select>
            </div>
            <div>
                <label for="filter-status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                <select id="filter-status" x-model="filters.status" class="h-11 w-full rounded-lg border border-border bg-white px-3 text-sm text-navy-900">
                    <option value="">Semua</option><option value="baik">Baik</option><option value="rusak">Rusak</option><option value="perbaikan">Perbaikan</option>
                </select>
            </div>
            <div>
                <label for="filter-year" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tahun anggaran</label>
                <select id="filter-year" x-model="filters.year" class="h-11 w-full rounded-lg border border-border bg-white px-3 text-sm text-navy-900">
                    <option value="">Semua</option>
                    <template x-for="year in yearList" :key="year"><option :value="String(year)" x-text="year"></option></template>
                </select>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <button type="button" @click="resetFilters()" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-border px-3 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50">
                <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.023 9.35A8 8 0 1 0 12 20m4-11h4V5" /></svg>
                Reset filter
            </button>
            <button type="button" @click="fitToKebumen()" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-border px-3 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50">
                <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3 3 5.5v15L9 18l6 3 6-2.5v-15L15 6 9 3Zm0 0v15m6-12v15" /></svg>
                Fokus Kebumen
            </button>
        </div>
    </section>

    <section class="relative overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
        <div class="relative h-[560px] min-h-[560px]" x-show="!error">
            <div id="asset-map" class="absolute inset-0 z-0"></div>
            <div class="pointer-events-none absolute bottom-4 left-4 z-[500] rounded-xl border border-border bg-white/95 p-3 shadow-md">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Legenda</p>
                <div class="space-y-1.5 text-xs text-slate-700">
                    <p class="flex items-center gap-2"><span class="inline-block h-3.5 w-3.5 rounded bg-teal-500"></span> Tiang LPJU</p>
                    <p class="flex items-center gap-2"><span class="inline-block h-3.5 w-3.5 rounded bg-amber-500"></span> Rambu lalu lintas</p>
                    <p class="flex items-center gap-2"><span class="inline-block h-2 w-4 rounded bg-red-500"></span> Status rusak</p>
                </div>
            </div>
        </div>

        <div x-cloak x-show="loading === true" class="absolute inset-0 z-[600] flex flex-col items-center justify-center gap-3 bg-white/85" :aria-hidden="loading !== true">
            <span class="h-8 w-8 animate-spin rounded-full border-[3px] border-teal-200 border-t-teal-600" aria-hidden="true"></span>
            <p class="text-sm font-medium text-slate-600">Memuat data aset...</p>
        </div>

        <div x-cloak x-show="error" role="alert" class="flex flex-col items-center justify-center gap-3 px-6 py-20 text-center">
            <svg class="h-10 w-10 text-red-500" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
            <p class="text-sm font-medium text-slate-700" x-text="error">Gagal memuat data.</p>
            <button type="button" @click="loadData()" class="min-h-10 rounded-lg bg-navy-900 px-4 text-sm font-semibold text-white hover:bg-navy-800">Coba lagi</button>
        </div>

        <div x-cloak x-show="!loading && !error && filtered.length === 0" class="flex flex-col items-center justify-center gap-2 px-6 py-20 text-center">
            <svg class="h-10 w-10 text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-5.2-5.2M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" /></svg>
            <p class="text-sm font-medium text-slate-700">Tidak ada aset yang cocok dengan filter.</p>
            <p class="text-xs text-slate-500">Coba ubah filter atau tambahkan data aset melalui panel operasional.</p>
        </div>
    </section>

    <p class="text-xs text-slate-500">Peta 2D dengan marker aset bergaya isometrik.</p>
</div>

<script>
    function assetMap() {
        return {
            map: null, loading: true, error: '',
            kebumenCenter: [-7.6786, 109.6565],
            assets: [], kecamatanList: [],
            search: '',
            filters: { type: '', kecamatan: '', status: '', year: '' },
            layers: { lpju: null, rambu: null },

            get yearList() {
                return [...new Set(this.assets.map(a => a.tahun_anggaran).filter(Boolean))].sort((a, b) => b - a);
            },
            get filtered() {
                const q = this.search.trim().toLowerCase();
                return this.assets.filter(a => {
                    if (this.filters.type && a.type !== this.filters.type) return false;
                    if (this.filters.kecamatan && String(a.desa?.kd_kec ?? '') !== this.filters.kecamatan) return false;
                    if (this.filters.status && (a.status || '') !== this.filters.status) return false;
                    if (this.filters.year && String(a.tahun_anggaran ?? '') !== this.filters.year) return false;
                    if (q && !(a.kode.toLowerCase().includes(q) || (a.alamat || '').toLowerCase().includes(q))) return false;
                    return true;
                });
            },

            async init() {
                try {
                    this.initMap();
                    await this.loadData();
                } catch (e) {
                    this.error = this.describeError(e);
                    console.error('[asset-map] initialization failed', e);
                } finally {
                    this.loading = false;
                }
                this.$watch('filtered', () => this.renderMarkers());
            },

            initMap() {
                const container = document.getElementById('asset-map');
                console.log('[asset-map] container size', {
                    width: container?.clientWidth,
                    height: container?.clientHeight,
                });
                if (!container) throw new Error('Container peta #asset-map tidak ditemukan.');
                if (this.map) this.map.remove();
                const map = window.Leaflet.map(container, {
                    center: this.kebumenCenter,
                    zoom: 11,
                    minZoom: 9,
                    maxZoom: 18,
                    scrollWheelZoom: false,
                });
                const tiles = window.Leaflet.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);
                tiles.on('tileerror', (event) => {
                    console.error('[asset-map] tile request failed', event.tile?.src);
                    this.error = 'Tile peta gagal dimuat. Periksa koneksi internet atau akses OpenStreetMap.';
                });
                map.on('load', () => this.logMapDiagnostics(map));
                this.layers.lpju = window.Leaflet.layerGroup().addTo(map);
                this.layers.rambu = window.Leaflet.layerGroup().addTo(map);
                this.map = map;
                this.fitToKebumen();
                setTimeout(() => {
                    map.invalidateSize();
                    this.logMapDiagnostics(map);
                }, 100);
            },

            logMapDiagnostics(map) {
                console.log('[asset-map] leaflet panes', map.getPanes());
                console.log('[asset-map] zoom controls', Boolean(document.querySelector('.leaflet-control-zoom')));
                console.log('[asset-map] tile count', document.querySelectorAll('.leaflet-tile').length);
            },

            fitToKebumen() {
                if (!this.map) return;
                this.map.setView(this.kebumenCenter, 11);
            },

            validCoordinates(asset) {
                const lat = Number.parseFloat(asset.latitude);
                const lng = Number.parseFloat(asset.longitude);
                return Number.isFinite(lat) && Number.isFinite(lng)
                    && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
            },

            fitToAssetsOrKebumen() {
                if (!this.map) return;
                const points = this.filtered.filter((asset) => this.validCoordinates(asset));
                if (points.length === 0) {
                    this.map.setView(this.kebumenCenter, 11);
                    return;
                }
                const bounds = window.Leaflet.latLngBounds(points.map((asset) => [Number.parseFloat(asset.latitude), Number.parseFloat(asset.longitude)]));
                this.map.fitBounds(bounds, { padding: [32, 32], maxZoom: 14 });
            },

            async loadData() {
                this.loading = true; this.error = '';
                try {
                    const endpoints = ['/api/lpju', '/api/rambu', '/api/kecamatan'];
                    const [lpjuRes, rambuRes, kecamatanRes] = await Promise.all(
                        endpoints.map((endpoint) => window.axios.get(endpoint, { headers: { Accept: 'application/json' } }))
                    );
                    const lpju = (lpjuRes.data || []).map((item, i) => ({ ...item, type: 'lpju', kode: `LPJU-${String(item.id ?? i + 1).padStart(4, '0')}` }));
                    const rambu = (rambuRes.data || []).map((item, i) => ({ ...item, type: 'rambu', kode: `RAMBU-${String(item.id ?? i + 1).padStart(4, '0')}` }));
                    this.assets = [...lpju, ...rambu];
                    this.kecamatanList = kecamatanRes.data || [];
                    this.renderMarkers();
                    this.fitToAssetsOrKebumen();
                } catch (e) {
                    this.error = this.describeError(e);
                    console.error('[asset-map] data request failed', e);
                } finally {
                    this.loading = false;
                    console.log('[asset-map] loading complete', {
                        loading: this.loading,
                        assets: this.filtered.length,
                    });
                }
            },

            describeError(error) {
                const response = error?.response;
                const body = response?.data;
                if (body?.message) return `Gagal memuat data (${response.status}): ${body.message}`;
                if (response) return `Gagal memuat data (${response.status} ${response.statusText || 'HTTP error'}).`;
                if (error?.request) return 'Gagal memuat data: server tidak merespons.';
                return `Gagal memuat data: ${error?.message || 'kesalahan tidak diketahui.'}`;
            },

            markerIcon(asset) {
                return window.assetMarkerIcon(asset);
            },

            popupHtml(asset) {
                const kec = asset.desa?.kecamatan?.kecamatan || asset.desa?.kec_dtks || '-';
                const rows = [
                    ['Kecamatan', kec],
                    ['Alamat', asset.alamat || '-'],
                    ['Status', asset.status || '-'],
                    ['Tahun anggaran', asset.tahun_anggaran ?? '-'],
                ];
                if (asset.type === 'rambu') rows.splice(1, 0, ['Jenis rambu', asset.jenis_rambu || '-']);
                if (asset.foto) rows.push(['Foto', `<a href="/storage/${asset.foto}" target="_blank" rel="noreferrer">Lihat foto</a>`]);
                return `<div class="asset-popup">
                    <p class="asset-popup__type">${asset.type === 'lpju' ? 'LPJU' : 'Rambu'}</p>
                    <p class="asset-popup__code">${asset.kode}</p>
                    <dl>${rows.map(([k, v]) => `<div><dt>${k}</dt><dd>${v}</dd></div>`).join('')}</dl>
                </div>`;
            },

            renderMarkers() {
                if (!this.map) return;
                this.layers.lpju.clearLayers();
                this.layers.rambu.clearLayers();
                for (const asset of this.filtered) {
                    const lat = parseFloat(asset.latitude), lng = parseFloat(asset.longitude);
                    if (!this.validCoordinates(asset)) continue;
                    const marker = window.Leaflet.marker([lat, lng], { icon: this.markerIcon(asset), title: asset.kode });
                    marker.bindTooltip(asset.kode, { direction: 'top', offset: [0, -50] });
                    marker.bindPopup(this.popupHtml(asset));
                    marker.addTo(asset.type === 'lpju' ? this.layers.lpju : this.layers.rambu);
                }
            },

            resetFilters() {
                this.search = '';
                this.filters = { type: '', kecamatan: '', status: '', year: '' };
                this.fitToKebumen();
            },
        };
    }
</script>
@endsection
