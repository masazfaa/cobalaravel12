    document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // 0. CEK DATA DARI LARAVEL
    // =========================================
    const dataAdmin = window.DATA_ADMIN;
    const dataJalan = window.DATA_JALAN;
    const dataMasjid = window.DATA_MASJID;

    // =========================================
    // 1. SETUP PETA DASAR & UI
    // =========================================

    var map = L.map('map').setView([-7.916181, 110.095629], 15);

    // Base Layers
    var baseLayers = [
        {
            name: "Open Street Map",
            layer: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OSM contributors'
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

    map.addLayer(baseLayers[1].layer); // Default OSM

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

    // ============================================================
    // 4. LOAD DATA: BATAS WILAYAH (POLYGON)
    // ============================================================

    // Cek apakah data dari Controller tersedia
    if (dataAdmin) {
        try {
            var layerAdmin = L.geoJSON(dataAdmin, {

                // A. STYLE VISUAL (Warna Biru Laut)
                style: function(feature) {
                    return {
                        fillColor: '#3498db',
                        fillOpacity: 0.6,
                        color: 'white',       // Warna garis tepi
                        weight: 1.5,          // Tebal garis
                        opacity: 1,
                        dashArray: '4, 4'     // Garis putus-putus
                    };
                },

                // B. INTERAKSI PER FITUR (Popup & Search)
                onEachFeature: function(feature, layer) {
                    // Alias biar ngetiknya pendek.
                    // p.nama_kolom harus SAMA PERSIS dengan database migration kamu
                    var p = feature.properties;

                    // 1. FORMAT NAMA UNTUK PENCARIAN (Search Plugin)
                    // Kita gabung Padukuhan + Kalurahan biar user gampang cari
                    var namaLengkap = (p.padukuhan || "Tanpa Nama") + " (" + (p.kalurahan || "-") + ")";

                    // Inject ke property 'nama' (karena plugin search cari field 'nama')
                    feature.properties.nama = namaLengkap;


                    // 2. SUSUN HTML POPUP (Tabel Detail)
                    // Menggunakan data: luas, jumlah_kk, jumlah_penduduk, jumlah_laki, jumlah_perempuan
                    var konten = `
                        <div style="min-width: 250px; font-family: sans-serif;">
                            <h4 style="margin:0; color:#007bff; border-bottom: 2px solid #eee; padding-bottom:5px;">
                                ${p.padukuhan}
                            </h4>
                            <small style="color:#666; display:block; margin-top:5px; font-style:italic;">
                                Kalurahan ${p.kalurahan}
                            </small>

                            <table style="width:100%; font-size:13px; margin-top:10px; border-collapse:collapse;">
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="padding:5px 0; color:#555;">Luas Wilayah</td>
                                    <td style="text-align:right; font-weight:bold;">${p.luas} Ha</td>
                                </tr>
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="padding:5px 0; color:#555;">Jumlah KK</td>
                                    <td style="text-align:right; font-weight:bold;">${p.jumlah_kk} KK</td>
                                </tr>
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="padding:5px 0; color:#555;">Total Penduduk</td>
                                    <td style="text-align:right; font-weight:bold;">${p.jumlah_penduduk} Jiwa</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px 0; color:#555;">Laki / Pr</td>
                                    <td style="text-align:right; font-weight:bold;">
                                        <span style="color:#2980b9">${p.jumlah_laki}</span> /
                                        <span style="color:#e74c3c">${p.jumlah_perempuan}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    `;

                    // Bind Popup ke Layer
                    layer.bindPopup(konten);

                    // 3. Masukkan ke Grup Pencarian
                    // Agar saat user ketik nama padukuhan, layer ini ketemu
                    searchGroup.addLayer(layer);

                    // (Opsional) Hover Effect
                    layer.on({
                        mouseover: function(e) {
                            var layer = e.target;
                            layer.setStyle({
                                weight: 3,
                                color: 'white', // Jadi kuning saat di-hover
                                dashArray: '',
                                fillOpacity: 0.8
                            });
                        },
                        mouseout: function(e) {
                            layerAdmin.resetStyle(e.target);
                        }
                    });
                }
            });

            // C. TAMPILKAN KE PETA
            layerAdmin.addTo(map);

            // D. DAFTARKAN KE PANEL LAYER (Kanan Atas)
            panelLayers.addOverlay({
                layer: layerAdmin,
                name: "Batas Padukuhan", // Nama di menu centang
                active: true             // Default tercentang
            }, "Batas Padukuhan");        // Masuk grup 'Polygon Layers'

        } catch (err) {
            console.error("Gagal Render Data Admin:", err);
        }
    }

    // ============================================================
    // 5. LOAD DATA: JALAN (LINESTRING)
    // ============================================================

    if (dataJalan) {
        try {
            var layerJalan = L.geoJSON(dataJalan, {

                // A. STYLE VISUAL BERDASARKAN KONDISI
                style: function(feature) {
                    // Ambil text kondisi, ubah ke Huruf Besar biar aman
                    var kondisi = (feature.properties.kondisi || '').toUpperCase();
                    var warna = '#7f8c8d'; // Default: Abu-abu (Jika data kosong)

                    // Logika Warna
                    if (kondisi.includes('BAIK')) {
                        warna = '#2ecc71'; // Hijau (Baik / Beton Baik)
                    } else if (kondisi.includes('SEDANG')) {
                        warna = '#f1c40f'; // Kuning/Orange
                    } else if (kondisi.includes('RUSAK') || kondisi.includes('BURUK')) {
                        warna = '#e74c3c'; // Merah
                    }

                    return {
                        color: warna,
                        weight: 4,       // Ketebalan garis
                        opacity: 0.8,    // Sedikit transparan
                        lineCap: 'round' // Ujung garis membulat
                    };
                },

                // B. INTERAKSI POPUP & SEARCH
                onEachFeature: function(feature, layer) {
                    var p = feature.properties;

                    // 1. Inject Nama untuk Search Plugin
                    feature.properties.nama = p.nama || "Jalan Tanpa Nama";

                    // 2. Format Rupiah (Helper Function)
                    var formatRupiah = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    });

                    // 3. Cek Foto (Tampilkan jika ada)
                    var htmlFoto = '';

                    // Kita tambahkan '/' di depan path biar browser ngarahin ke public directory dengan benar
                    if (p.foto_awal) {
                        var imgAwal = p.foto_awal.startsWith('http') ? p.foto_awal : '/' + p.foto_awal;
                        htmlFoto += `<div style="margin-top:5px;"><small class="fw-bold">Foto Awal:</small><br><img src="${imgAwal}" style="width:100%; height:120px; object-fit:cover; border-radius:4px; border:1px solid #ccc;"></div>`;
                    }
                    if (p.foto_akhir) {
                        var imgAkhir = p.foto_akhir.startsWith('http') ? p.foto_akhir : '/' + p.foto_akhir;
                        htmlFoto += `<div style="margin-top:5px;"><small class="fw-bold">Foto Akhir:</small><br><img src="${imgAkhir}" style="width:100%; height:120px; object-fit:cover; border-radius:4px; border:1px solid #ccc;"></div>`;
                    }

                    // 4. Susun Popup (Tabel Lengkap)
                    var konten = `
                        <div style="min-width: 280px; font-family: sans-serif;">
                            <h4 style="margin:0; color:#c0392b; border-bottom: 2px solid #eee; padding-bottom:5px;">
                                ${p.nama || '-'}
                            </h4>

                            <div style="margin: 8px 0;">
                                <span style="background:#34495e; color:white; padding:2px 6px; border-radius:4px; font-size:11px;">
                                    ${p.kewenangan || 'Tanpa Kewenangan'}
                                </span>
                                <span style="background:${p.status == 'Aktif' ? '#27ae60' : '#95a5a6'}; color:white; padding:2px 6px; border-radius:4px; font-size:11px;">
                                    ${p.status || '-'}
                                </span>
                            </div>

                            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="color:#666;">Panjang / Lebar</td>
                                    <td style="text-align:right;"><b>${p.panjang} m</b> x <b>${p.lebar} m</b></td>
                                </tr>
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="color:#666;">Luas Area</td>
                                    <td style="text-align:right;"><b>${p.luas} m²</b></td>
                                </tr>
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="color:#666;">Kondisi</td>
                                    <td style="text-align:right;"><b>${p.kondisi || '-'}</b></td>
                                </tr>
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="color:#666;">Asal Usul</td>
                                    <td style="text-align:right;">${p.asal || '-'}</td>
                                </tr>
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="color:#666;">NJOP Meter</td>
                                    <td style="text-align:right;">${formatRupiah.format(p.rer_njop)}</td>
                                </tr>
                                <tr style="background-color:#f9f9f9;">
                                    <td style="color:#666; padding:3px;">Total Aset</td>
                                    <td style="text-align:right; font-weight:bold; color:#27ae60;">
                                        ${formatRupiah.format(p.aset_tanah)}
                                    </td>
                                </tr>
                            </table>

                            ${htmlFoto}
                        </div>
                    `;

                    layer.bindPopup(konten);
                    searchGroup.addLayer(layer);

                    // Efek Hover (Garis menebal saat mouse lewat)
                    layer.on({
                        mouseover: function(e) {
                            var l = e.target;
                            l.setStyle({ weight: 7, opacity: 1 });
                            l.bringToFront(); // Biar garisnya muncul di atas poligon
                        },
                        mouseout: function(e) {
                            layerJalan.resetStyle(e.target);
                        }
                    });
                }
            });

            // C. TAMPILKAN KE PETA
            layerJalan.addTo(map);

            // D. INTEGRASI PANEL LAYER
            panelLayers.addOverlay({
                layer: layerJalan,
                name: "Jaringan Jalan",
                active: true
            }, "Jaringan Jalan"); // Masuk Grup 'DATA JALUR'

        } catch (err) {
            console.error("Gagal Render Data Jalan:", err);
        }
    }

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

    // ============================================================
    // 6. LOAD DATA: MASJID (POINT)
    //    (Cluster & Heatmap pakai satu sumber data: window.DATA_MASJID)
    // ============================================================

    if (dataMasjid) {
        try {

            // -------------------------------------------------------------
            // BAGIAN A: MARKER CLUSTER (TITIK)
            // -------------------------------------------------------------

            // Konfigurasi Group Cluster (Lingkaran Warna-warni)
            var markers = L.markerClusterGroup({
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: true,
                zoomToBoundsOnClick: true,

                // Custom Icon untuk Cluster (Hijau -> Kuning -> Merah)
                iconCreateFunction: function (cluster) {
                    var count = cluster.getChildCount();
                    var c = 'cluster-small'; // Default Hijau (< 10)

                    if (count >= 10 && count < 20) {
                        c = 'cluster-medium'; // Kuning (10-20)
                    } else if (count >= 20) {
                        c = 'cluster-large';  // Merah (> 20)
                    }

                    return L.divIcon({
                        html: '<div><span>' + count + '</span></div>',
                        className: 'custom-cluster ' + c,
                        iconSize: L.point(40, 40)
                    });
                }
            });

            // Definisi Layer GeoJSON Masjid
            var layerMasjid = L.geoJSON(dataMasjid, {

                // 1. Ganti Titik Biru Standar jadi Icon Masjid
                pointToLayer: function(feature, latlng) {
                    return L.marker(latlng, {
                        icon: L.divIcon({
                            className: 'pin-leaflet', // CSS class untuk pin
                            // Ikon FontAwesome Masjid
                            html: '<i class="fa-solid fa-mosque" style="font-size: 16px;"></i>',

                            iconSize: [34, 34],   // Ukuran Kotak Pin
                            iconAnchor: [17, 34], // Ujung bawah pin (biar pas di lokasi)
                            popupAnchor: [0, -34] // Posisi popup di atas pin
                        })
                    });
                },

                // 2. Isi Popup & Fitur Search
                onEachFeature: function(feature, layer) {
                    var p = feature.properties;

                    // Inject Nama untuk Search Plugin
                    feature.properties.nama = p.nama || "Masjid Tanpa Nama";

                    // --- LOGIKA TOMBOL WHATSAPP ---
                    var btnWA = '';
                    if (p.no_telepon) {
                        // Bersihkan nomor (hapus spasi/-) dan ubah 08xx jadi 628xx
                        var rawNum = p.no_telepon.toString().replace(/\D/g, '');
                        if (rawNum.startsWith('0')) {
                            rawNum = '62' + rawNum.substring(1);
                        }

                        btnWA = `
                            <a href="https://wa.me/${rawNum}?text=Assalamualaikum, mohon info mengenai kegiatan di masjid ${p.nama}"
                               target="_blank"
                               style="display:inline-block; margin-top:5px; background:#25D366; color:white; padding:3px 8px; border-radius:3px; text-decoration:none; font-size:11px;">
                               <i class="fa-brands fa-whatsapp"></i> Chat Takmir
                            </a>
                        `;
                    }

                    // --- LOGIKA FOTO ---
                    var htmlFoto = '';
                    if (p.foto) {
                        // Cukup tambahin '/' (garis miring) di depan path fotonya.
                        // Nanti otomatis ngarah ke http://cobalaravel12.test/masjid/foto.jpg
                        var imgUrl = '/' + p.foto;

                        htmlFoto = `<div style="margin-bottom:8px;">
                                        <img src="${imgUrl}" style="width:100%; height:120px; object-fit:cover; border-radius:4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    </div>`;
                    }

                    // --- SUSUN POPUP ---
                    var popupContent = `
                        <div style="min-width: 250px; text-align:left; font-family: sans-serif;">
                            ${htmlFoto}

                            <h4 style="margin:0; color:#006400; border-bottom: 2px solid #eee; padding-bottom:5px;">
                                ${p.nama}
                            </h4>

                            <table style="width:100%; font-size:12px; margin-top:5px;">
                                <tr>
                                    <td style="color:#666;">Luas Area</td>
                                    <td align="right"><b>${p.luas_m2 || 0} m²</b></td>
                                </tr>
                                <tr>
                                    <td style="color:#666;">Kapasitas</td>
                                    <td align="right"><b>${p.jumlah_jamaah || 0} Jamaah</b></td>
                                </tr>
                                <tr>
                                    <td style="color:#666;">Takmir</td>
                                    <td align="right">${p.takmir_cp || '-'}</td>
                                </tr>
                                <tr>
                                    <td style="color:#666;">Kontak</td>
                                    <td align="right">${p.no_telepon || '-'}</td>
                                </tr>
                            </table>

                            <div style="margin-top:8px; text-align:center;">
                                ${btnWA}
                            </div>
                        </div>
                    `;

                    layer.bindPopup(popupContent);
                    searchGroup.addLayer(layer); // Daftarkan ke pencarian
                }
            });

            // Tambahkan Layer Masjid ke dalam Cluster Group
            markers.addLayer(layerMasjid);

            // Tampilkan Cluster ke Peta
            map.addLayer(markers);

            // Masukkan ke Panel Layer (Grup: Titik Lokasi)
            panelLayers.addOverlay({
                layer: markers,
                name: "Sebaran Masjid (Cluster)",
                active: true
            }, "Masjid");


            // -------------------------------------------------------------
            // BAGIAN B: HEATMAP (PETA KEPADATAN)
            // -------------------------------------------------------------
            // Kita gunakan data yang sama, tidak perlu fetch ulang dari server

            if (typeof L.heatLayer === 'function') {
                var heatPoints = [];

                dataMasjid.features.forEach(function(f) {
                    // Cek koordinat valid atau tidak
                    if (f.geometry && f.geometry.coordinates) {
                        // GeoJSON: [Lon, Lat] -> Leaflet Heatmap: [Lat, Lon, Intensity]
                        var lat = f.geometry.coordinates[1];
                        var lng = f.geometry.coordinates[0];

                        // Intensitas bisa statis (0.5) atau dinamis berdasarkan kapasitas jamaah
                        // Contoh dinamis: (f.properties.jumlah_jamaah / 1000)
                        heatPoints.push([lat, lng, 0.6]);
                    }
                });

                var heatLayer = L.heatLayer(heatPoints, {
                    radius: 25,  // Jarak sebaran
                    blur: 15,    // Efek blur/asap
                    maxZoom: 16, // Zoom maksimal efek heatmap
                    gradient: {
                        0.4: 'blue',   // Sedikit
                        0.65: 'lime',  // Sedang
                        1.0: 'red'     // Padat
                    }
                });

                // Default Heatmap HIDDEN (biar tidak numpuk sama cluster)
                // heatLayer.addTo(map);

                // Tambahkan ke Panel Layer
                panelLayers.addOverlay({
                    layer: heatLayer,
                    name: "Heatmap Kepadatan Masjid"
                }, "Kepadatan Masjid");
            }

        } catch (err) {
            console.error("Gagal Render Data Masjid:", err);
        }
    }

    });



