document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // 0. CEK DEPENDENSI DARI BLADE
    // =========================================
    // Kita ambil nilai yang sudah diset di file Blade (@push js)
    const BASE_URL = window.APP_URL;
    const TOKEN = window.CESIUM_TOKEN;
    const RAW_DATA = window.DATA_GEDUNG_RAW || [];

    // Validasi agar tidak error "undefined"
    if (!BASE_URL || !TOKEN) {
        console.error("❌ Error: APP_URL atau CESIUM_TOKEN belum diset di Blade/View.");
        console.error("Pastikan script ini dipanggil SETELAH definisi variabel window.");
        return; // Hentikan eksekusi script
    }

    // Set Token Cesium Ion dari variabel global
    Cesium.Ion.defaultAccessToken = TOKEN;

    console.log("🚀 Cesium dimulai dengan Base URL:", BASE_URL);


    // =========================================
    // 1. UI & GLOBAL FUNCTIONS
    // =========================================

    window.toggleMenu = function() {
        var menu = document.getElementById('menu-dropdown');
        var icon = document.getElementById('menu-icon');
        if (menu.style.display === 'block') {
            menu.style.display = 'none';
            if(icon) icon.className = 'fa-solid fa-caret-down';
        } else {
            menu.style.display = 'block';
            if(icon) icon.className = 'fa-solid fa-caret-up';
        }
    };

    document.addEventListener('click', function(e) {
        var header = document.getElementById('header-box');
        var menu = document.getElementById('menu-dropdown');
        var icon = document.getElementById('menu-icon');
        if (header && menu && !header.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none';
            if(icon) icon.className = 'fa-solid fa-caret-down';
        }
    });


    // =========================================
    // 2. KONFIGURASI DASAR CESIUM
    // =========================================

    const viewer = new Cesium.Viewer('cesiumContainer', {
        baseLayerPicker: false,
        geocoder: false,
        homeButton: false,
        infoBox: true,
        sceneModePicker: false,
        selectionIndicator: false,
        navigationHelpButton: false,
        navigationInstructionsInitiallyVisible: false,
        timeline: false,
        animation: false,
        fullscreenButton: true,
        terrainProvider: undefined, // Nanti di-load manual di fungsi async
        imageryProvider: true      // Matikan default imagery (Bing Maps)
    });

    // Aktifkan Depth Test (Wajib untuk 3D agar tidak tembus tanah)
    viewer.scene.globe.depthTestAgainstTerrain = true;

    // POSISI KAMERA AWAL
    viewer.camera.setView({
        destination: Cesium.Cartesian3.fromDegrees(110.372480, -7.765125, 1000),
        orientation: {
            heading: Cesium.Math.toRadians(0.0),
            pitch: Cesium.Math.toRadians(-90.0),
            roll: 0.0
        }
    });


    // =========================================
    // 3. FUNGSI UTAMA (ASYNC LOAD ASSETS)
    // =========================================

    async function mulaiWebGIS() {
        try {
            viewer.entities.removeAll();

            const baseLayer = await Cesium.IonImageryProvider.fromAssetId(2);
            viewer.imageryLayers.addImageryProvider(baseLayer);

            // --- BAGIAN A: CITRA (OFFLINE/LOCAL) ---
            const layerCitra = new Cesium.UrlTemplateImageryProvider({
                url: BASE_URL + "/citra/csrtftugm/{z}/{x}/{reverseY}.png",
                tilingScheme: new Cesium.WebMercatorTilingScheme(),
                minimumLevel: 12, maximumLevel: 21,
                rectangle: Cesium.Rectangle.fromDegrees(110.360, -7.780, 110.390, -7.750),
                hasAlphaChannel: true
            });

            // Mencegah spam error di console jika tile tidak ditemukan
            layerCitra.errorEvent.addEventListener(e => e.retry = false);
            viewer.imageryLayers.addImageryProvider(layerCitra);


            // --- BAGIAN B: TERRAIN (OFFLINE/LOCAL) ---
            try {
                const terrainProvider = await Cesium.CesiumTerrainProvider.fromUrl(
                    BASE_URL + "/terrain/terrain_ftugm",
                    { requestVertexNormals: true }
                );
                viewer.scene.setTerrain(new Cesium.Terrain(terrainProvider));
            } catch (errTerrain) {
                console.warn("⚠️ Gagal memuat Terrain lokal. Cek path folder.", errTerrain);
            }


            // --- BAGIAN C: MODEL 3D DINAMIS (ARRAY & LOOP) ---

            // Mapping ulang data dari format Database ke format Cesium
            const daftarGedung = RAW_DATA.map(function(item) {
                return {
                    name: item.name,
                    // Rakit HTML untuk Popup
                    desc: `
                        <div style="padding:10px">
                            <p><b>${item.name}</b></p>
                            <p>${item.description || '-'}</p>
                        </div>
                    `,
                    // Gabungkan Base URL dengan path dari database
                    uri: BASE_URL + item.model_path,

                    // Pastikan format angka aman (Parse Float wajib untuk data DB)
                    long: parseFloat(item.longitude),
                    lat: parseFloat(item.latitude),
                    height: parseFloat(item.height) || 0,
                    heading: parseFloat(item.heading) || 0
                };
            });

            console.log(`✅ Memuat ${daftarGedung.length} gedung dari database.`);

            // Looping: Mesin pembuat gedung otomatis
            daftarGedung.forEach(data => {

                // Validasi koordinat (Cegah crash jika data kosong/NaN)
                if (isNaN(data.long) || isNaN(data.lat)) {
                    console.warn(`Gedung ${data.name} dilewati karena koordinat tidak valid.`);
                    return;
                }

                // 1. Hitung Posisi XYZ
                const posisi = Cesium.Cartesian3.fromDegrees(data.long, data.lat, data.height);

                // 2. Hitung Orientasi (Arah Hadap)
                const hpr = new Cesium.HeadingPitchRoll(Cesium.Math.toRadians(data.heading), 0, 0);
                const orientasi = Cesium.Transforms.headingPitchRollQuaternion(posisi, hpr);

                // 3. Tambahkan ke Viewer sebagai Entity
                viewer.entities.add({
                    name: data.name,
                    description: data.desc,
                    position: posisi,
                    orientation: orientasi,
                    model: {
                        uri: data.uri,
                        scale: 1.0,
                        heightReference: Cesium.HeightReference.CLAMP_TO_GROUND,
                        silhouetteColor: Cesium.Color.WHITE.withAlpha(0.5),
                        silhouetteSize: 0.0 // Default mati, nyala saat diklik
                    }
                });
            });


            // --- BAGIAN D: LOGIKA HIGHLIGHT (SILHOUETTE) ---
            // Listener untuk mendeteksi gedung mana yang dipilih
            viewer.selectedEntityChanged.addEventListener(function(entity) {
                // Matikan semua highlight dulu
                const entities = viewer.entities.values;
                for (let i = 0; i < entities.length; i++) {
                    let ent = entities[i];
                    if (ent.model) {
                        ent.model.silhouetteSize = 0.0;
                    }
                }

                // Nyalakan highlight yang dipilih
                if (Cesium.defined(entity) && entity.model) {
                    entity.model.silhouetteSize = 2.0;
                    entity.model.silhouetteColor = Cesium.Color.CYAN; // Ubah warna biar kelihatan beda
                }
            });

        } catch (error) {
            console.error("❌ Error Fatal WebGIS:", error);
        }
    }

    // Jalankan Fungsi Utama
    mulaiWebGIS();


    // =========================================
    // 4. FUNGSI PENCARIAN
    // =========================================

    window.cariObjek = function() {
        const input = document.getElementById('search-input');
        if(!input) return;

        const keyword = input.value.toLowerCase();
        if (!keyword) { alert("Masukkan kata kunci!"); return; }

        console.log("Mencari: " + keyword);

        const entities = viewer.entities.values;
        let foundEntity = null;

        // Linear Search
        for (let i = 0; i < entities.length; i++) {
            const entity = entities[i];
            if (entity.name && entity.name.toLowerCase().includes(keyword)) {
                foundEntity = entity;
                break;
            }
        }

        if (foundEntity) {
            // Animasi Terbang
            viewer.flyTo(foundEntity, {
                duration: 2.0,
                offset: new Cesium.HeadingPitchRange(
                    Cesium.Math.toRadians(0),
                    Cesium.Math.toRadians(-45), // Sudut pandang serong
                    200 // Jarak zoom lebih dekat
                )
            }).then(function() {
                // Buka Popup & Trigger Highlight
                viewer.selectedEntity = foundEntity;
            });
        } else {
            alert("Objek tidak ditemukan: " + keyword);
        }
    };

    // Event Listener Tombol Enter
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                window.cariObjek();
            }
        });
    }

});
