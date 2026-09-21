<script>
    window.assetSignKind = function (value) {
        const text = String(value || '').toLowerCase();
        if (text.includes('peringatan') || text.includes('warning')) return 'warning';
        if (text.includes('larangan') || text.includes('prohibition')) return 'prohibition';
        if (text.includes('perintah') || text.includes('command')) return 'command';
        return 'guide';
    };

    window.assetMarkerIcon = function (asset) {
        const broken = (asset.status || '') === 'rusak';
        if (asset.type === 'lpju') {
            return window.Leaflet.divIcon({
                className: 'asset-marker-icon', iconSize: [58, 76], iconAnchor: [29, 72], popupAnchor: [0, -68],
                html: `<div class="asset-marker asset-marker--lpju ${broken ? 'asset-marker--broken' : ''}">
                    <svg width="58" height="76" viewBox="0 0 58 76" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <defs><radialGradient id="lpju-glow-${asset.type}-${asset.id}" cx="50%" cy="50%"><stop offset="0" stop-color="#99f6e4" stop-opacity=".65"/><stop offset="1" stop-color="#14b8a6" stop-opacity="0"/></radialGradient></defs>
                        <ellipse cx="31" cy="71" rx="17" ry="4" fill="#0f172a" opacity=".24"/>
                        <polygon points="25,18 33,20 33,69 25,72" fill="#0f766e"/><polygon points="25,18 18,21 18,69 25,72" fill="#14b8a6"/>
                        <path d="M25 20 C25 12 34 10 43 14 L43 18 C35 15 30 17 30 23" fill="none" stroke="#0f766e" stroke-width="4" stroke-linecap="round"/>
                        <path d="M39 14 L48 17 L42 22 L35 19 Z" fill="#334155"/><ellipse cx="42" cy="20" rx="16" ry="13" fill="url(#lpju-glow-${asset.type}-${asset.id})"/>
                        <ellipse cx="42" cy="19" rx="5" ry="3" fill="${broken ? '#94a3b8' : '#ccfbf1'}"/>
                    </svg>
                </div>`,
            });
        }
        const kind = window.assetSignKind(asset.jenis_rambu);
        const shapes = {
            warning: '<polygon points="29,5 48,22 29,39 10,22" fill="#f59e0b" stroke="#92400e" stroke-width="3"/><polygon points="29,9 44,22 29,35 14,22" fill="#fef3c7"/>',
            prohibition: '<circle cx="29" cy="22" r="18" fill="#f8fafc" stroke="#dc2626" stroke-width="6"/><path d="M17 10 L41 34" stroke="#dc2626" stroke-width="4"/>',
            command: '<circle cx="29" cy="22" r="18" fill="#2563eb" stroke="#1e3a8a" stroke-width="3"/><path d="M29 11v22M20 22h18" stroke="white" stroke-width="3"/>',
            guide: '<rect x="8" y="7" width="42" height="30" rx="2" fill="#f59e0b" stroke="#92400e" stroke-width="3"/><path d="M15 22h28M29 14v16" stroke="#fffbeb" stroke-width="3"/>',
        };
        return window.Leaflet.divIcon({
            className: 'asset-marker-icon', iconSize: [58, 78], iconAnchor: [29, 74], popupAnchor: [0, -70],
            html: `<div class="asset-marker asset-marker--rambu-${kind} ${broken ? 'asset-marker--broken' : ''}">
                <svg width="58" height="78" viewBox="0 0 58 78" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <ellipse cx="31" cy="73" rx="17" ry="4" fill="#0f172a" opacity=".24"/><polygon points="27,34 34,35 34,70 27,73" fill="#92400e"/><polygon points="27,34 21,36 21,70 27,73" fill="#f59e0b"/>
                    ${shapes[kind]}
                </svg>
            </div>`,
        });
    };
</script>
