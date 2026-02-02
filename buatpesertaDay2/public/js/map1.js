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


// ==========================================
// 1. LOAD DATA POLIGON (ADMINISTRASI)
// ==========================================

// Panggil file dari folder geojson
fetch('geojson/adminKw.geojson')
    .then(response => response.json()) // Ubah jadi format JSON
    .then(data => {

        // Inisialisasi Layer GeoJSON
        var layerAdmin = L.geoJSON(data, {

            // --- KITA AKAN ISI STYLE DI SINI (SLIDE 47) ---
            style: function(feature) {
                return {
                    fillColor: '#3498db', // Warna Biru Laut
                    fillOpacity: 0.6,     // Transparansi (0 - 1)
                    color: 'white',       // Garis tepi Putih
                    weight: 1,            // Ketebalan garis
                    opacity: 1,
                    dashArray: '3, 3'     // Garis putus-putus
                };
            },

            // --- KITA AKAN ISI POPUP DI SINI (SLIDE 48) ---
            onEachFeature: function(feature, layer) {
            // Simpan properti di variabel 'p' biar ngetiknya pendek
            var p = feature.properties;
            feature.properties.nama = p.Padukuhan + " (Desa " + p.Kalurahan + ")";
            // Susun HTML Popup (Tabel)
            var konten = `
                <div style="min-width: 200px;">
                    <h4 style="margin:0; color:#007bff;">${p.Padukuhan}</h4>
                    <small style="color:#666;">Kalurahan ${p.Kalurahan}</small>
                    <hr style="margin:5px 0; border:0; border-top:1px solid #ccc;">

                    <table style="width:100%; font-size:12px; border-collapse:collapse;">
                        <tr>
                            <td style="color:#666;">Luas</td>
                            <td style="font-weight:bold; text-align:right;">${p.LUAS} Ha</td>
                        </tr>
                        <tr>
                            <td>Jml Penduduk</td>
                            <td style="font-weight:bold; text-align:right;">${p.JUM_PDDK} Jiwa</td>
                        </tr>
                        <tr>
                            <td>Laki / Pr</td>
                            <td style="font-weight:bold; text-align:right;">${p.JUM_LAKI2} / ${p.JUM_PEREMP}</td>
                        </tr>
                    </table>
                </div>
            `;

            layer.bindPopup(konten);

            // Daftarkan layer ini ke fitur Pencarian
            searchGroup.addLayer(layer);
        },

        });

        // --- KITA AKAN INTEGRASI PANEL DI SINI (SLIDE 49) ---
        // 1. Tampilkan Layer ke Peta
        layerAdmin.addTo(map);

        // 2. Masukkan ke Panel Layer Kanan
        panelLayers.addOverlay({
            layer: layerAdmin,    // Objek layernya
            name: "Batas Padukuhan", // Teks di Panel
            active: true          // Centang otomatis
        }, "Polygon Layers");     // Nama Grup di Panel

    })
    .catch(error => console.error("Gagal load data:", error));


// ==========================================
// 2. LOAD DATA GARIS (JALAN)
// ==========================================
fetch('geojson/jalankw.geojson')
    .then(response => response.json())
    .then(data => {

        var layerJalan = L.geoJSON(data, {
            style: function(feature) {
                // Kita bedakan warna berdasarkan 'Kondisi' (Opsional)
                var warna = '#e74c3c'; // Default Merah
                if (feature.properties.Kondisi === 'BETON-BAIK') { warna = '#27ae60'; } // Hijau jika bagus

                return {
                    color: warna,
                    weight: 3,
                    opacity: 1,
                };
            },
            onEachFeature: function(feature, layer) {
                var p = feature.properties;

                // === 1. SYARAT WAJIB SEARCH ===
                // Copy "Nama" (dari JSON) ke "nama" (untuk Plugin Search)
                feature.properties.nama = p.Nama;
                // ==============================

                // 2. Popup Detail Tabel
                var popupContent = `
                    <div style="min-width: 200px;">
                        <h4 style="margin:0; color:#c0392b;">${p.Nama}</h4>
                        <small>Kewenangan: ${p.Kewenangan}</small>
                        <hr style="margin:5px 0; border:0; border-top:1px solid #ccc;">

                        <table style="width:100%; font-size:12px;">
                            <tr><td>Panjang</td><td>: <b>${p.Panjang} m</b></td></tr>
                            <tr><td>Lebar</td><td>: <b>${p.Lebar} m</b></td></tr>
                            <tr><td>Kondisi</td><td>: <b>${p.Kondisi}</b></td></tr>
                            <tr><td>Status</td><td>: <b>${p.Status}</b></td></tr>
                        </table>
                    </div>
                `;

                layer.bindPopup(popupContent);

                // 3. Masukkan ke Search Group (Wajib)
                searchGroup.addLayer(layer);
            }
        });

        layerJalan.addTo(map);
        // Integrasi ke Panel Layers
        panelLayers.addOverlay({
            layer: layerJalan,
            name: "Jaringan Jalan"
        }, "DATA JALUR"); // Nama Grup Panel
    });

    // fetch('geojson/masjid.geojson')
    // .then(response => response.json())
    // .then(data => {

    //     var layerMasjid = L.geoJSON(data, {
    //         // OPSI 1: L.circleMarker
    //         // Gunakan ini jika ingin tampilan simpel
    //         // pointToLayer: function(feature, latlng) {
    //         //     return L.circleMarker(latlng, {
    //         //         radius: 8,
    //         //         fillColor: '#006400', // Hijau Tua
    //         //         color: '#fff',
    //         //         weight: 2,
    //         //         fillOpacity: 1
    //         //     });
    //         // },

    //         pointToLayer: function(feature, latlng) {
    //             return L.marker(latlng, {
    //                 icon: L.divIcon({
    //                     className: 'pin-leaflet',
    //                     html: '<i class="fa-solid fa-mosque"></i>',

    //                     iconSize: [34, 34], // Sesuaikan dengan CSS

    //                     // PENTING: Titik tancap digeser ke ujung bawah pin
    //                     // [X, Y] -> X=setengah lebar, Y=tinggi total + sedikit gap
    //                     iconAnchor: [17, 34],

    //                     popupAnchor: [0, -34] // Popup muncul di atas pin
    //                 })
    //             });
    //         },

    //         // OPSI 3: L.icon (Gambar PNG Custom)
    //         // Gunakan ini jika punya gambar icon sendiri
    //         // pointToLayer: function(feature, latlng) {
    //         //     var myIcon = L.icon({
    //         //         iconUrl: 'geojson/shopping-cart.png', // Pastikan file ada di folder public/geojson
    //         //         iconSize: [32, 32],
    //         //         iconAnchor: [16, 32],
    //         //         popupAnchor: [0, -28]
    //         //     });
    //         //     return L.marker(latlng, { icon: myIcon });
    //         // },
    //         onEachFeature: function(feature, layer) {
    //             var p = feature.properties;
    //             feature.properties.nama = p.Nama;
    //             var popupContent = `
    //                 <div style="text-align:center;">
    //                     <h5 style="margin:0; font-weight:bold; color:#006400;">${p.Nama}</h5>
    //                     <small>Fasilitas Ibadah</small>
    //                 </div>
    //             `;
    //             layer.bindPopup(popupContent);
    //             searchGroup.addLayer(layer);
    //         }
    //     });
    //     layerMasjid.addTo(map);
    //     panelLayers.addOverlay({
    //         layer: layerMasjid,
    //         name: "Sebaran Masjid"
    //     }, "Titik Masjid");
    // });

    // ==========================================
    // 3. LOAD DATA TITIK (MASJID) CLUSTER
    // ==========================================
    fetch('geojson/masjid.geojson')
        .then(response => response.json())
        .then(data => { // <--- PINTU MASUK DATA DIBUKA DI SINI

            // ----------------------------------------------------------------
            // BAGIAN 1: MARKER CLUSTER
            // ----------------------------------------------------------------

            var markers = L.markerClusterGroup({

                // Fungsi ini otomatis jalan saat titik-titik menyatu
                iconCreateFunction: function (cluster) {

                    // 1. Hitung jumlah titik dalam kelompok ini
                    var count = cluster.getChildCount();

                    // 2. Tentukan mau pakai kelas CSS warna apa?
                    var c = 'cluster-small'; // Default Hijau (< 10)

                    if (count >= 5 && count < 10) {
                        c = 'cluster-medium'; // Jadi Kuning (5-10)
                    } else if (count >= 10) {
                        c = 'cluster-large';  // Jadi Merah (> 10)
                    }

                    // 3. Kembalikan Icon Custom
                    return L.divIcon({
                        // Tampilkan angkanya di tengah
                        html: '<div><span>' + count + '</span></div>',

                        // Gabungkan class dasar + class warna
                        className: 'custom-cluster ' + c,

                        // Ukuran lingkaran (40x40 pixel)
                        iconSize: L.point(40, 40)
                    });
                },

                // Opsi Tambahan (Biar animasi smooth)
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: true,
                zoomToBoundsOnClick: true
            });

            var layerMasjid = L.geoJSON(data, {
            pointToLayer: function(feature, latlng) {
                return L.marker(latlng, {
                    icon: L.divIcon({
                        className: 'pin-leaflet',
                        html: '<i class="fa-solid fa-mosque"></i>',

                        iconSize: [34, 34], // Sesuaikan dengan CSS

                        // PENTING: Titik tancap digeser ke ujung bawah pin
                        // [X, Y] -> X=setengah lebar, Y=tinggi total + sedikit gap
                        iconAnchor: [17, 34],

                        popupAnchor: [0, -34] // Popup muncul di atas pin
                    })
                });
            },
                onEachFeature: function(feature, layer) {
                    var p = feature.properties;
                    feature.properties.nama = p.Nama;

                    var popupContent = `
                        <div style="text-align:center;">
                            <h5 style="margin:0; font-weight:bold; color:#006400;">${p.Nama}</h5>
                            <small>Fasilitas Ibadah</small>
                        </div>
                    `;
                    layer.bindPopup(popupContent);
                    searchGroup.addLayer(layer);
                }
            });

            markers.addLayer(layerMasjid);
            map.addLayer(markers);

            panelLayers.addOverlay({
                layer: markers,
                name: "Sebaran Masjid (Cluster)"
            }, "Masjid Cluster");
        })
        .catch(err => console.error("Gagal load masjid:", err));

        // ==========================================
        // 3b. LOAD DATA MASJID (FITUR HEATMAP)
        // ==========================================
        fetch('geojson/masjid.geojson')
            .then(response => response.json()) // Download ulang file yang sama
            .then(data => {

                // 1. Ekstrak Koordinat [Lat, Lng, Intensitas]
                var heatData = data.features.map(function(f) {
                    return [f.geometry.coordinates[1], f.geometry.coordinates[0], 0.5];
                });

                // 2. Buat Layer Heatmap
                var heatLayer = L.heatLayer(heatData, {
                    radius: 30, // 1. Ukuran Radius (Makin besar makin menyatu)
                    blur: 20, // 2. Kehalusan (Makin besar makin nge-blur seperti asap)

                    // 3. Sensitivitas Zoom
                    // Kalau diset 10: Zoom jauh aja udah merah banget.
                    // Kalau diset 18: Harus zoom dekat banget baru kelihatan merah.
                    maxZoom: 17,
                    gradient: {
                        0.4: 'blue',    // Kepadatan Rendah (Biru)
                        0.65: 'lime',   // Kepadatan Sedang (Hijau terang)
                        1.0: 'red'      // Kepadatan Tinggi (Merah)
                    }
                });

                // 3. Masukkan ke Panel Layer
                // (Default tidak ditampilkan ke map, biar user centang sendiri)

                heatLayer.addTo(map);

                panelLayers.addOverlay({
                    layer: heatLayer,
                    name: "Peta Kepadatan (Heatmap)"
                }, "Heatmap Masjid");

            }) // Tutup fetch kedua
            .catch(err => console.error("Gagal load heatmap:", err));

    });



