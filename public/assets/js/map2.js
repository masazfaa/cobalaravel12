    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('map').setView([-7.916181, 110.095629], 15);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

// Definisikan fungsi secara GLOBAL menggunakan 'window.'
    window.toggleMenu = function() {
        var menu = document.getElementById('menu-dropdown');
        var icon = document.getElementById('menu-icon');

        // Cek kondisi saat ini
        if (menu.style.display === 'block') {
            menu.style.display = 'none'; // Sembunyikan

            // Ubah panah jadi ke bawah (jika pakai FontAwesome)
            if(icon) icon.className = 'fa-solid fa-caret-down';
        } else {
            menu.style.display = 'block'; // Munculkan

            // Ubah panah jadi ke atas
            if(icon) icon.className = 'fa-solid fa-caret-up';
        }
    };

    // Tambahan: Klik di mana saja (luar menu) untuk menutup
    document.addEventListener('click', function(e) {
        var header = document.getElementById('header-box');
        var menu = document.getElementById('menu-dropdown');
        var icon = document.getElementById('menu-icon');

        // Jika klik BUKAN di header dan BUKAN di menu
        if (header && menu && !header.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none'; // Tutup paksa
            if(icon) icon.className = 'fa-solid fa-caret-down';
        }
    });

    // Event: Saat mouse bergerak di atas peta
map.on('mousemove', function(e) {
    // Ambil elemen span
    var latSpan = document.getElementById('lat-val');
    var lngSpan = document.getElementById('lng-val');

    // Isi dengan koordinat dari event (e.latlng)
    // toFixed(5) artinya ambil 5 angka belakang koma
    if (latSpan && lngSpan) {
        latSpan.innerText = e.latlng.lat.toFixed(5);
        lngSpan.innerText = e.latlng.lng.toFixed(5);
    }
});

var searchGroup = new L.LayerGroup()

// 2. Inisialisasi Kontrol Pencarian
var searchControl = new L.Control.Search({
    layer: searchGroup,      // Target pencarian (sementara kosong)
    propertyName: 'nama',    // Field yang dicari
    marker: false,           // Jangan kasih marker merah default
    collapsed: false,  // Agar input box langsung muncul (tidak perlu klik ikon)


    textPlaceholder: 'Cari lokasi...', // Opsional: Teks bantuan
moveToLocation: function(latlng, title, map) {
    map.flyTo(latlng, 17);

    if (latlng.layer) {
        // Tunggu 1 detik (1000ms) sampai zoom selesai, baru buka popup
        setTimeout(function() {
            latlng.layer.openPopup();
        }, 1500);
    }
}
});

// 3. Ambil Wadah Custom kita
var searchWrapper = document.getElementById('search-wrapper');

// 4. Masukkan Tombol Search ke dalam Wadah tersebut
if (searchWrapper) {
    // Tambahkan dulu ke map agar fungsi internalnya jalan
    map.addControl(searchControl);

    // LALU PINDAHKAN elemen HTML-nya ke div kita
    searchWrapper.appendChild(searchControl.getContainer());
}

// 1. Definisikan Peta Dasar
var baseLayers = [
    {
        name: "Open Street Map",
        layer: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OSM contributors'
        }),
        // Ikon FontAwesome sebagai thumbnail
        icon: '<i class="fa-solid fa-map" style="color:#555;"></i>'
    },
    {
        name: "Google Satellite",
        layer: L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            attribution: 'Google'
        }),
        icon: '<i class="fa-solid fa-satellite" style="color:#555;"></i>'
    }
];

map.addLayer(baseLayers[1].layer);


// 2. Buat Tombol Custom
var PanelBtn = L.Control.extend({
    options: { position: 'topright' }, // Posisi sama di kanan atas

    onAdd: function(map) {
        var btn = L.DomUtil.create('div', 'custom-layer-btn');
        btn.innerHTML = '<i class="fa-solid fa-layer-group"></i>';
        btn.title = "Layer List";

        // LOGIKA KLIK YANG BENAR
        btn.onclick = function(e) {
            L.DomEvent.stopPropagation(e); // Cegah klik tembus ke peta

            // Ambil elemen HTML asli dari Panel Layers
            var panelContainer = panelLayers.getContainer();

            // Cek: Apakah sedang sembunyi?
            if (panelContainer.style.display === 'none') {
                panelContainer.style.display = 'block'; // MUNCULKAN
            } else {
                panelContainer.style.display = 'none';  // SEMBUNYIKAN
            }
        };
        return btn;
    }
});
// Tambahkan tombol ke peta
map.addControl(new PanelBtn());


// 3. Data Layer Overlay (Sementara Kosong Dulu)
// Nanti diisi data GeoJSON
var overLayers = [
    {
        group: "Polygon Layers",
        layers: []
    }
];

// 4. Pasang Panel Layers
var panelLayers = new L.Control.PanelLayers(baseLayers, overLayers, {
    collapsibleGroups: true, // Bisa dilipat per grup
    collapsed: false,        // Panel terbuka isinya
    position: 'topright',    // Posisi kanan atas
    compact: true            // Tampilan padat
});

map.addControl(panelLayers);

// // 1. WMS Poligon (Admin)
// var wmsAdmin = L.tileLayer.wms('http://localhost:8080/geoserver/latihan_leaflet/wms', {
//     layers: 'latihan_leaflet:adminkw',
//     format: 'image/png',
//     transparent: true
// });

// // 2. WMS Garis (Jalan)
// var wmsJalan = L.tileLayer.wms('http://localhost:8080/geoserver/latihan_leaflet/wms', {
//     layers: 'latihan_leaflet:jalankw',
//     format: 'image/png',
//     transparent: true
// });

// // 3. WMS Titik (Masjid)
// // Note: Ikon masjidnya nanti sesuai style bawaan GeoServer (biasanya kotak/titik aja)
// var wmsMasjid = L.tileLayer.wms('http://localhost:8080/geoserver/latihan_leaflet/wms', {
//     layers: 'latihan_leaflet:masjid',
//     format: 'image/png',
//     transparent: true
// });

// // Masukkan ke Panel Layer (Grup: TAMPILAN SERVER)
// // Jangan lupa .addTo(map) jika ingin tampil default
// wmsAdmin.addTo(map);

// panelLayers.addOverlay({layer: wmsAdmin, name: "Admin (WMS)"}, "DATA SERVER (WMS)");
// panelLayers.addOverlay({layer: wmsJalan, name: "Jalan (WMS)"}, "DATA SERVER (WMS)");
// panelLayers.addOverlay({layer: wmsMasjid, name: "Masjid (WMS)"}, "DATA SERVER (WMS)");







// URL WFS untuk Admin
// var urlAdminWFS = "http://localhost:8080/geoserver/latihan_leaflet/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=latihan_leaflet:adminkw&outputFormat=application/json";

// fetch(urlAdminWFS)
//     .then(response => response.json())
//     .then(data => {
//         var layerAdminWFS = L.geoJSON(data, {
//             style: { fillColor: 'blue', fillOpacity: 0.2, color: 'blue' }, // Styling di JS
//             onEachFeature: function(feature, layer) {
//                 layer.bindPopup("Wilayah: " + feature.properties.Padukuhan);
//             }
//         });

//         // Masukkan Panel
//         panelLayers.addOverlay({layer: layerAdminWFS, name: "Admin (WFS Interaktif)"}, "DATA SERVER (WFS)");
//     });


// // --- 1. WFS JALAN ---
// var urlJalan = "http://localhost:8080/geoserver/latihan_leaflet/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=latihan_leaflet:jalankw&outputFormat=application/json";

// fetch(urlJalan).then(res => res.json()).then(data => {
//     var layerJalanWFS = L.geoJSON(data, {
//         style: { color: 'red', weight: 3 },
//         onEachFeature: function(feature, layer) {
//             layer.bindPopup("Jalan: " + feature.properties.Nama);
//         }
//     });
//     panelLayers.addOverlay({layer: layerJalanWFS, name: "Jalan (WFS)"}, "DATA SERVER (WFS)");
// });

// // --- 2. WFS MASJID ---
// var urlMasjid = "http://localhost:8080/geoserver/latihan_leaflet/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=latihan_leaflet:masjid&outputFormat=application/json";

// fetch(urlMasjid).then(res => res.json()).then(data => {
//     var layerMasjidWFS = L.geoJSON(data, {
//         pointToLayer: function(f, latlng) {
//             return L.circleMarker(latlng, { radius: 6, fillColor: 'green', color: 'white', weight: 1, fillOpacity: 1 });
//         },
//         onEachFeature: function(feature, layer) {
//             layer.bindPopup("Masjid: " + feature.properties.Nama);
//         }
//     });
//     panelLayers.addOverlay({layer: layerMasjidWFS, name: "Masjid (WFS)"}, "DATA SERVER (WFS)");
// });

    // ==========================================
    // LAYER VISUAL (WMS - GAMBAR DARI GEOSERVER)
    // ==========================================
    // Tujuannya: Agar peta terlihat cantik sesuai styling SLD di GeoServer

    var wmsAdmin = L.tileLayer.wms('http://localhost:8080/geoserver/latihan_leaflet/wms', {
        layers: 'latihan_leaflet:adminkw',
        format: 'image/png',
        transparent: true
    });

    var wmsJalan = L.tileLayer.wms('http://localhost:8080/geoserver/latihan_leaflet/wms', {
        layers: 'latihan_leaflet:jalankw',
        format: 'image/png',
        transparent: true
    });

    var wmsMasjid = L.tileLayer.wms('http://localhost:8080/geoserver/latihan_leaflet/wms', {
        layers: 'latihan_leaflet:masjid',
        format: 'image/png',
        transparent: true
    });

    // Tampilkan WMS secara default
    wmsAdmin.addTo(map);
    wmsJalan.addTo(map);
    wmsMasjid.addTo(map);

    // Masukkan ke Panel
    panelLayers.addOverlay({layer: wmsAdmin, name: "Batas Wilayah", active: true}, "TAMPILAN (WMS)");
    panelLayers.addOverlay({layer: wmsJalan, name: "Jaringan Jalan", active: true}, "TAMPILAN (WMS)");
    panelLayers.addOverlay({layer: wmsMasjid, name: "Sebaran Masjid", active: true}, "TAMPILAN (WMS)");


    // ==========================================
    // 6. LAYER LOGIKA (WFS - DATA INTERAKTIF & SEARCH)
    // ==========================================
    // Tujuannya: Agar bisa diklik (Popup) dan Dicari.
    // Kita buat TRANSPARAN total agar tidak menutupi gambar WMS.

    // --- A. WFS ADMIN (Poligon) ---
    var urlWfsAdmin = "http://localhost:8080/geoserver/latihan_leaflet/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=latihan_leaflet:adminkw&outputFormat=application/json";

    fetch(urlWfsAdmin).then(r => r.json()).then(data => {
        var layerAdminWFS = L.geoJSON(data, {
            // STYLE INVISIBLE
            style: { color: 'transparent', fillColor: 'transparent', fillOpacity: 0 },
            onEachFeature: function(feature, layer) {
                var p = feature.properties;
                // Normalisasi Nama (Cari Padukuhan atau Nama)
                feature.properties.nama = p.Padukuhan || p.PADUKUHAN || p.Nama || "Wilayah";

                layer.bindPopup("Wilayah: " + feature.properties.nama);
                searchGroup.addLayer(layer); // Masuk ke pencarian
            }
        }).addTo(map); // Wajib add to map agar bisa diklik
    });

    // --- B. WFS JALAN (Garis) ---
    var urlWfsJalan = "http://localhost:8080/geoserver/latihan_leaflet/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=latihan_leaflet:jalankw&outputFormat=application/json";

    fetch(urlWfsJalan).then(r => r.json()).then(data => {
        var layerJalanWFS = L.geoJSON(data, {
            // STYLE INVISIBLE (Tapi tebal biar gampang diklik)
            style: { color: 'transparent', weight: 15, opacity: 0 },
            onEachFeature: function(feature, layer) {
                var p = feature.properties;
                feature.properties.nama = p.Nama || p.NAMA || "Jalan";

                layer.bindPopup("Jalan: " + feature.properties.nama);
                searchGroup.addLayer(layer);
            }
        }).addTo(map);
    });

    // --- C. WFS MASJID (Titik) ---
    var urlWfsMasjid = "http://localhost:8080/geoserver/latihan_leaflet/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=latihan_leaflet:masjid&outputFormat=application/json";

    fetch(urlWfsMasjid).then(r => r.json()).then(data => {
        var layerMasjidWFS = L.geoJSON(data, {
            // STYLE INVISIBLE (Radius agak besar biar gampang diklik jari/mouse)
            pointToLayer: function(f, latlng) {
                return L.circleMarker(latlng, { radius: 10, color: 'transparent', fillOpacity: 0 });
            },
            onEachFeature: function(feature, layer) {
                var p = feature.properties;
                // Normalisasi Nama (Sesuai console log Bapak tadi: 'Nama')
                feature.properties.nama = p.Nama || p.NAMA || "Masjid";

                layer.bindPopup("Masjid: " + feature.properties.nama);
                searchGroup.addLayer(layer);
            }
        }).addTo(map);
    });

// ==========================================
    // 7. LAYER RASTER (WMTS - UNTUK VISUALISASI)
    // ==========================================
    // Pastikan layer 'krwn' sudah ada di GeoServer

    // Pola URL GeoWebCache (GWC) di GeoServer:
    // http://localhost:8080/geoserver/gwc/service/tms/1.0.0/WORKSPACE:LAYER@EPSG%3A900913@png/{z}/{x}/{y}.png
    // EPSG:900913 adalah kode lain dari Web Mercator (Google Maps projection)

    var wmtsUrl = 'http://localhost:8080/geoserver/gwc/service/tms/1.0.0/latihan_leaflet:krwn@EPSG%3A900913@png/{z}/{x}/{y}.png';

    var layerKrwnWMTS = L.tileLayer(wmtsUrl, {
        tms: true, // WAJIB TRUE! Karena sistem koordinat tile GeoServer terbalik (Y-axis)
        maxZoom: 22, // Sesuaikan dengan zoom level maksimal GeoServer
        opacity: 1.0,
        attribution: 'Data Raster Karangwuni'
    });

    // Masukkan ke Panel Layer sebagai Overlay (Bisa dimati-nyalakan)
    // Kita taruh di grup baru "DATA RASTER"
    panelLayers.addOverlay({
        layer: layerKrwnWMTS,
        name: "Foto Udara (WMTS)",
        active: false // Default mati biar ringan diawal
    }, "DATA RASTER");


    // ==========================================
    // 8. LAYER WCS (DOWNLOADER)
    // ==========================================

    // 1. Siapkan URL WCS (GetCoverage)
    // Format: service=WCS & request=GetCoverage & coverageId=WORKSPACE:LAYER & format=image/geotiff
    var wcsUrl = "http://localhost:8080/geoserver/latihan_leaflet/wcs?service=WCS&version=2.0.1&request=GetCoverage&coverageId=latihan_leaflet:krwn&format=image/geotiff";

    // 2. Buat Layer "Pancingan" (Dummy Layer)
    // Kita pakai LayerGroup kosong, tujuannya cuma buat mancing event 'add'
    var wcsLayer = L.layerGroup();

    // 3. Event Listener: Apa yang terjadi saat layer ini dicentang?
    wcsLayer.on('add', function() {
        // Tampilkan pesan edukasi
        alert("PERHATIAN: Layer WCS bukan untuk ditampilkan di peta!\n\nLayanan ini memberikan DATA ASLI (GeoTIFF). Browser akan otomatis mendownload file tersebut. Silakan buka file hasil download menggunakan QGIS/ArcGIS.");

        // Eksekusi Download
        window.open(wcsUrl, '_self');

        // Opsional: Matikan lagi centangnya biar ga bingung (karena layer ini cuma tombol download)
        setTimeout(function(){
            map.removeLayer(wcsLayer);
            // Note: Di panel layer mungkin centangnya tetap nyala tergantung plugin, tapi layer sudah removed.
        }, 1000);
    });

    // 4. Masukkan ke Panel Layer
    panelLayers.addOverlay({
        layer: wcsLayer,
        name: "<i class='fa-solid fa-download'></i> Download Data Mentah (WCS)", // Pakai ikon biar jelas
        active: false
    }, "DATA RASTER");


// ==========================================
// 9. LAYER STATIC TILES (GDAL2TILES / XYZ)
// ==========================================

// Arahkan ke folder tempat hasil generate gdal2tiles disimpan
// Pastikan folder 'citra' ada di dalam folder 'public' project Laravel
var layerStatic = L.tileLayer('/citra/krwn/{z}/{x}/{y}.png', {

    // UBAH JADI TRUE
    // Ini memerintahkan Leaflet: "Tolong hitung koordinatnya dari bawah, jangan dari atas"
    tms: true,

    minZoom: 12, // Sesuaikan dengan folder zoom terkecil yg Bapak punya
    maxZoom: 21, // Sesuaikan dengan folder zoom terbesar
    attribution: 'Peta Statis',
    errorTileUrl: 'https://upload.wikimedia.org/wikipedia/commons/c/ca/1x1.png'
});
// Masukkan ke Panel
panelLayers.addBaseLayer({
    layer: layerStatic,
    name: "Foto Udara (File Statis)"
}, "DATA RASTER");

});



