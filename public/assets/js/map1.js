    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('map').setView([-0.7893, 113.9213], 5);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
    });
