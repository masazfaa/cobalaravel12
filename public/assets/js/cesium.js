document.addEventListener('DOMContentLoaded', function () {

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

    // Token tetap diperlukan jika nanti ada fallback ke asset ion
    if (window.CESIUM_TOKEN) {
        Cesium.Ion.defaultAccessToken = window.CESIUM_TOKEN;
    } else {
        console.warn("Token Cesium belum diset. Pastikan semua aset offline tersedia.");
    }

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
        terrainProvider: undefined, // Nanti di-load manual
    });

    // Aktifkan Depth Test (Wajib untuk 3D)
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

            // --- BAGIAN A: CITRA (OFFLINE/LOCAL) ---
            const layerCitra = new Cesium.UrlTemplateImageryProvider({
                url: window.APP_URL + "/citra/csrtftugm/{z}/{x}/{reverseY}.png",
                tilingScheme: new Cesium.WebMercatorTilingScheme(),
                minimumLevel: 12, maximumLevel: 21,
                rectangle: Cesium.Rectangle.fromDegrees(110.360, -7.780, 110.390, -7.750),
                hasAlphaChannel: true
            });
            layerCitra.errorEvent.addEventListener(e => e.retry = false);
            viewer.imageryLayers.addImageryProvider(layerCitra);


            // --- BAGIAN B: TERRAIN (OFFLINE/LOCAL) ---
            const terrainProvider = await Cesium.CesiumTerrainProvider.fromUrl(
                window.APP_URL + "/terrain/terrain_ftugm",
                { requestVertexNormals: true }
            );
            viewer.scene.setTerrain(new Cesium.Terrain(terrainProvider));


            // --- BAGIAN C: MODEL 3D DINAMIS (ARRAY & LOOP) ---
            // Cukup edit bagian ini untuk menambah/mengurangi gedung
            const daftarGedung = [
                {
                    name: "Gedung SGLC UGM",
                    desc: `<div style="padding:10px"><p><b>SGLC UGM</b></p><p>Smart Green Learning Center.</p></div>`,
                    uri: window.APP_URL + "/data3d/Smart Green Learning Center UGM.glb",
                    long: 110.372406,
                    lat: -7.765341,
                    height: 0,
                    heading: 90 // Rotasi 90 derajat
                },
                {
                    name: "Gedung ERIC UGM",
                    desc: `<div style="padding:10px"><p><b>ERIC UGM</b></p><p>Engineering Research and Innovation Center.</p></div>`,
                    uri: window.APP_URL + "/data3d/ERIC_UGM.glb", // Pastikan file ada
                    long: 110.372600, // Sesuaikan koordinat asli
                    lat: -7.765500,
                    height: 0,
                    heading: 0
                }
                // Tambah gedung lain di sini...
            ];

            // Looping: Mesin pembuat gedung otomatis
            daftarGedung.forEach(data => {
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
                viewer.entities.values.forEach(ent => {
                    if (ent.model) ent.model.silhouetteSize = 0.0;
                });

                // Nyalakan highlight yang dipilih
                if (Cesium.defined(entity) && entity.model) {
                    entity.model.silhouetteSize = 2.0;
                }
            });

        } catch (error) {
            console.error("Error WebGIS:", error);
        }
    }

    // Jalankan Fungsi Utama
    mulaiWebGIS();


    // =========================================
    // 4. FUNGSI PENCARIAN
    // =========================================

    window.cariObjek = function() {
        const keyword = document.getElementById('search-input').value.toLowerCase();
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
                    0 // Range 0 = Auto zoom fit
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
    document.getElementById('search-input').addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            window.cariObjek();
        }
    });

});
