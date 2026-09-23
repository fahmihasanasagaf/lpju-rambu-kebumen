<script>
    window.locationMarkerIcon = function () {
        return window.Leaflet.divIcon({
            className: 'location-marker-icon',
            iconSize: [42, 52],
            iconAnchor: [21, 49],
            popupAnchor: [0, -48],
            html: `<div class="location-marker" title="Lokasi dipilih" aria-label="Lokasi dipilih">
                <svg width="42" height="52" viewBox="0 0 42 52" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M21 2C10.5 2 2 10.3 2 20.5 2 34.2 21 50 21 50s19-15.8 19-29.5C40 10.3 31.5 2 21 2Z" fill="#0f172a" stroke="#fff" stroke-width="2"/>
                    <circle cx="21" cy="20" r="9" fill="#14b8a6" stroke="#ccfbf1" stroke-width="3"/>
                    <circle cx="21" cy="20" r="3" fill="#fff"/>
                </svg>
            </div>`,
        });
    };
</script>
