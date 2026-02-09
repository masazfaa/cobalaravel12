    document.addEventListener('DOMContentLoaded', function () {
    // =========================================
    // 0. CEK DATA DARI CONTROLLER
    // =========================================
    const layersConfig = window.GEOSERVER_LAYERS;
    const baseURL = window.APP_URL;


    // =========================================
    // 1. SETUP PETA DASAR
    // =========================================

    var map = L.map('map').setView([-7.916181, 110.095629], 15);

    var baseLayers = [
        {
            name: "Open Street Map",
            layer: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }),
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

    // =========================================
    // 2. KONTROL PENCARIAN & PANEL LAYER
    // =========================================
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
    // =========================================
    // 3. UI GLOBAL (MENU TOGGLE)
    // =========================================
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

    // =========================================
    // 4. CORE ENGINE: LOOPING DATA DARI DB
    // =========================================

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

// =========================================
    // 4. CORE ENGINE: LOOPING DATA DARI DB
    // =========================================

    // Kita loop setiap konfigurasi layer yang ada di database
    layersConfig.forEach(function(config) {

        // Bersihkan Base URL (tambahkan slash di akhir jika belum ada)
        // Contoh: "http://localhost:8080/geoserver" -> "http://localhost:8080/geoserver/"
        var cleanBaseUrl = config.base_url.replace(/\/$/, "") + "/";
        var fullLayerName = config.workspace + ':' + config.layer_name;

        // --- A. TIPE VECTOR (Admin, Jalan, Masjid) ---
        if (config.type === 'vector') {

            // 1. VISUALISASI (WMS)
            // --------------------
            if (config.enable_wms) {
                var wmsLayer = L.tileLayer.wms(cleanBaseUrl + 'wms', {
                    layers: fullLayerName,
                    format: 'image/png',
                    transparent: true,
                    zIndex: config.z_index // Urutan tumpukan sesuai DB
                });

                // Tambahkan ke Peta jika 'is_active' true
                if(config.is_active) wmsLayer.addTo(map);

                // Tambahkan ke Panel Layer
                panelLayers.addOverlay({
                    layer: wmsLayer,
                    name: config.title,
                    active: config.is_active
                }, "Vector Layers (WMS)");
            }


            // 2. INTERAKSI & PENCARIAN (WFS)
            // ------------------------------
            if (config.enable_wfs) {
                // Susun URL WFS (Format GeoJSON)
                var wfsUrl = cleanBaseUrl + "ows?service=WFS&version=1.0.0&request=GetFeature&typeName=" +
                             fullLayerName + "&outputFormat=application/json";

                // Fetch Data GeoJSON dari GeoServer
                fetch(wfsUrl)
                    .then(response => response.json())
                    .then(data => {
                        var interactionLayer = L.geoJSON(data, {
                            // Style Transparan (Invisible) tapi bisa diklik
                            style: {
                                color: 'transparent',
                                fillColor: 'transparent',
                                fillOpacity: 0,
                                weight: 15 // Garis tebal biar gampang diklik
                            },
                            pointToLayer: function(f, latlng) {
                                // Titik radius besar transparan
                                return L.circleMarker(latlng, { radius: 10, stroke: false, fillOpacity: 0 });
                            },
                            onEachFeature: function(feature, layer) {
                                var p = feature.properties;

                                // === KUNCI PENCARIAN (FIX) ===
                                // Cari kolom yang kira-kira adalah Nama, lalu simpan ke 'nama' (huruf kecil)
                                // Ini agar plugin Leaflet-Search bisa menemukannya.
                                var labelNama = p.nama || p.Nama || p.NAMA ||       // Cek variasi nama
                                                p.Padukuhan || p.PADUKUHAN ||       // Cek wilayah
                                                p.Kecamatan ||                      // Cek kecamatan
                                                p.Jalan ||                          // Cek jalan
                                                config.title;                    // Fallback

                                feature.properties.nama = labelNama; // INJECT PROPERTI 'nama'

                                // === GENERATE POPUP OTOMATIS ===
                                var popupTable = '<div style="max-height:200px; overflow-y:auto;"><table style="width:100%; font-size:12px; border-collapse:collapse;">';

                                // Loop semua kolom data yang ada di GeoServer
                                for (var key in p) {
                                    // Filter: Jangan tampilkan bbox atau object geometry
                                    if (p.hasOwnProperty(key) && key !== 'bbox' && typeof p[key] !== 'object') {
                                        popupTable += `
                                            <tr style="border-bottom:1px solid #eee;">
                                                <td style="color:#666; text-transform:capitalize;">${key}</td>
                                                <td style="font-weight:bold; text-align:right;">${p[key]}</td>
                                            </tr>`;
                                    }
                                }
                                popupTable += '</table></div>';

                                var popupContent = `
                                    <h4 style="margin:0; color:#007bff; border-bottom:2px solid #ddd;">${labelNama}</h4>
                                    <small>${config.title}</small>
                                    ${popupTable}
                                `;

                                layer.bindPopup(popupContent);

                                // Masukkan ke grup pencarian
                                searchGroup.addLayer(layer);
                            }
                        });

                        // Wajib addTo(map) agar event klik jalan, walau tak terlihat
                        interactionLayer.addTo(map);

                    })
                    .catch(err => console.error(`Gagal load WFS ${config.layer_name}:`, err));
            }
        }


        // --- B. TIPE RASTER (Foto Udara / Citra) ---
        else if (config.type === 'raster') {

            // 1. WMTS (Visual Cepat via GWC)
            if (config.enable_wmts) {
                // Pola URL GeoWebCache (EPSG:900913)
                var wmtsUrl = `${cleanBaseUrl}gwc/service/tms/1.0.0/${fullLayerName}@EPSG%3A900913@png/{z}/{x}/{y}.png`;

                var rasterLayer = L.tileLayer(wmtsUrl, {
                    tms: true, // Wajib true untuk GWC
                    maxZoom: 22,
                    opacity: 1.0,
                    attribution: config.title
                });

                // Tambahkan ke Panel (Default non-aktif biar ringan)
                panelLayers.addOverlay({
                    layer: rasterLayer,
                    name: config.title + " (WMTS)",
                    active: config.is_active
                }, "Raster Layers");

                if(config.is_active) rasterLayer.addTo(map);
            }

            // 2. FITUR DOWNLOAD (WCS)
            // Tombol dummy di panel layer untuk download file asli (GeoTIFF)
            var downloadLayer = L.layerGroup(); // Layer kosong

            // URL WCS GetCoverage
            var wcsUrl = `${cleanBaseUrl}wcs?service=WCS&version=2.0.1&request=GetCoverage&coverageId=${fullLayerName}&format=image/geotiff`;

            downloadLayer.on('add', function() {
                var konfirmasi = confirm(`Apakah Anda ingin mendownload data mentah (GeoTIFF) untuk ${config.title}?`);
                if (konfirmasi) {
                    window.open(wcsUrl, '_self');
                }
                // Matikan centang lagi otomatis setelah diklik
                setTimeout(() => map.removeLayer(downloadLayer), 500);
            });

            panelLayers.addOverlay({
                layer: downloadLayer,
                name: `<i class="fa-solid fa-download"></i> Download ${config.title}`,
                active: false
            }, "Download Data");

        }

    }); // End Loop Configuration


    // =========================================
    // 5. STATIC LAYER (MANUAL)
    // =========================================
    // Khusus untuk layer file statis (XYZ) lokal

    var layerStatic = L.tileLayer(baseURL + '/citra/krwn/{z}/{x}/{y}.png', {
        tms: true,
        minZoom: 12,
        maxZoom: 21,
        attribution: 'Citra Statis'
    });

    panelLayers.addBaseLayer({
        layer: layerStatic,
        name: "Foto Udara (File Statis)"
    }, "Raster Layers");


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



