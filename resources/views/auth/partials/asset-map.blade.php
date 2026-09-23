<div id="login-asset-map" class="auth-map" aria-hidden="true"></div>
<script>
    (() => {
        const escapeHtml = (value) => String(value ?? '-').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
        }[character]));

        const bootMap = () => {
            if (!window.Leaflet) return;
            const node = document.getElementById('login-asset-map');
            if (!node || node.dataset.ready) return;
            node.dataset.ready = 'true';

            const map = window.Leaflet.map(node, {
                center: [-7.6786, 109.6565],
                zoom: 11,
                minZoom: 9,
                maxZoom: 18,
                scrollWheelZoom: false,
                zoomControl: false,
            });
            window.Leaflet.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);

            const validAssets = [];
            const lpjuLayer = window.Leaflet.layerGroup().addTo(map);
            const rambuLayer = window.Leaflet.layerGroup().addTo(map);
            const normalize = (response) => Array.isArray(response?.data)
                ? response.data
                : (Array.isArray(response) ? response : (Array.isArray(response?.data?.data) ? response.data.data : []));
            const showAssets = (items, type, layer) => (items || []).forEach((item, index) => {
                const asset = {
                    ...item,
                    type,
                    kode: `${type === 'lpju' ? 'LPJU' : 'RAMBU'}-${String(item.id ?? index + 1).padStart(4, '0')}`,
                };
                const latitude = Number.parseFloat(asset.latitude);
                const longitude = Number.parseFloat(asset.longitude);
                if (!Number.isFinite(latitude) || !Number.isFinite(longitude)
                    || latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) return;

                validAssets.push(asset);
                const marker = window.Leaflet.marker([latitude, longitude], {
                    icon: window.assetMarkerIcon(asset),
                    title: asset.kode,
                    alt: `${asset.type} ${asset.kode}`,
                });
                marker.bindTooltip(asset.kode, { direction: 'top', offset: [0, -58] });
                marker.bindPopup(`<strong>${escapeHtml(asset.kode)}</strong><br>${escapeHtml(asset.alamat)}<br>Status: ${escapeHtml(asset.status)}`);
                marker.addTo(layer);
            });
            const focusAssets = () => {
                if (!validAssets.length) return;
                map.fitBounds(window.Leaflet.latLngBounds(validAssets.map((asset) => [
                    Number.parseFloat(asset.latitude), Number.parseFloat(asset.longitude),
                ])), { padding: [28, 28], maxZoom: 14 });
            };

            document.getElementById('login-map-focus')?.addEventListener('click', focusAssets);
            Promise.all([window.axios.get('/api/lpju'), window.axios.get('/api/rambu')])
                .then(([lpju, rambu]) => {
                    showAssets(normalize(lpju), 'lpju', lpjuLayer);
                    showAssets(normalize(rambu), 'rambu', rambuLayer);
                    focusAssets();
                })
                .catch(() => {})
                .finally(() => {
                    map.invalidateSize();
                    window.setTimeout(() => map.invalidateSize(), 150);
                });

            window.setTimeout(() => map.invalidateSize(), 100);
        };

        document.addEventListener('DOMContentLoaded', bootMap);
        window.addEventListener('load', bootMap);
    })();
</script>
