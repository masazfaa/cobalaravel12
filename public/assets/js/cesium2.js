document.addEventListener('DOMContentLoaded', async function () {

    // =========================================
    // 0. CEK DEPENDENSI DARI BLADE
    // =========================================
    const BASE_URL = window.APP_URL;
    const TOKEN = window.CESIUM_TOKEN;
    const RAW_DATA = window.DATA_GEDUNG_RAW || [];

    // Validasi Token
    if (TOKEN) {
        Cesium.Ion.defaultAccessToken = TOKEN;
    } else {
        console.error("❌ Token Cesium belum diset di .env!");
        return;
    }

    console.log(`🚀 Cesium Ion Mode: Memuat ${RAW_DATA.length} aset dari database.`);


    // =========================================
    // 1. UI & GLOBAL FUNCTIONS (Menu & Header)
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
        terrainProvider: undefined, // Kita set manual di bawah
    });

    // Aktifkan Depth Test agar objek menyatu dengan terrain
    viewer.scene.globe.depthTestAgainstTerrain = true;

    // Posisi Kamera Awal (UGM)
    viewer.camera.setView({
        destination: Cesium.Cartesian3.fromDegrees(110.372480, -7.765125, 1000),
        orientation: {
            heading: Cesium.Math.toRadians(0.0),
            pitch: Cesium.Math.toRadians(-90.0),
            roll: 0.0
        }
    });


    // =========================================
    // 3. MEMUAT ASET SECARA DINAMIS (SCALABLE)
    // =========================================

    // A. Load Terrain (Default World Terrain / Custom ID)
    try {
        // Gunakan ID Terrain FT UGM jika ada, atau default Cesium World Terrain
        // ID 1 = Cesium World Terrain (Default)
        // Ganti 2976593 dengan ID Terrain Ion kamu jika punya aset terrain khusus
        viewer.scene.setTerrain(
            new Cesium.Terrain(
                await Cesium.CesiumTerrainProvider.fromIonAssetId(2976593)
            )
        );
    } catch (e) {
        console.warn("⚠️ Gagal memuat custom terrain, fallback ke default.", e);
        viewer.scene.terrainProvider = await Cesium.createWorldTerrainAsync();
    }


    // B. LOOPING: Memuat Gedung dari DATABASE
    // Menggunakan RAW_DATA dari Controller Laravel

    if (RAW_DATA.length === 0) {
        console.warn("⚠️ Data Cesium Ion kosong di Database.");
    }

    for (const data of RAW_DATA) {
        try {
            // Validasi ID Aset
            if (!data.ion_asset_id) {
                console.warn(`Data gedung ${data.name} tidak memiliki ion_asset_id.`);
                continue;
            }

            // 1. Load Tileset dari Cesium Ion
            // Perhatikan: Kita pakai 'data.ion_asset_id' sesuai nama kolom DB
            const tileset = await Cesium.Cesium3DTileset.fromIonAssetId(data.ion_asset_id);

            viewer.scene.primitives.add(tileset);

            // 2. Trik Zoom: Gunakan Bounding Sphere
            // Kita butuh menunggu tileset siap (ready) untuk dapat koordinat tengahnya
            await tileset.readyPromise;
            const center = tileset.boundingSphere.center;

            // 3. Buat Entity "Bayangan" untuk Popup & Pencarian
            // 3D Tiles tidak punya popup bawaan, jadi kita buat titik transparan di tengahnya
            const contentHtml = `
                <div style="padding:10px">
                    <p><b>${data.name}</b></p>
                    <div>${data.description || '-'}</div>
                </div>
            `;

            const entity = viewer.entities.add({
                name: data.name,
                position: center, // Titik tengah aset 3D
                point: {
                    pixelSize: 10,
                    color: Cesium.Color.TRANSPARENT // Titik tidak terlihat
                },
                description: contentHtml
            });

            // 4. LINKING: Tempelkan Entity ke Tileset
            // Ini trik agar saat 3D Tiles diklik, Entity-nya yang terpilih
            tileset._linkedEntity = entity;
            tileset._name = data.name; // Simpan nama di tileset juga

            console.log(`✅ Berhasil memuat: ${data.name} (ID: ${data.ion_asset_id})`);

        } catch (error) {
            console.error(`❌ Gagal memuat gedung ${data.name} (ID: ${data.ion_asset_id}):`, error);
        }
    }


    // =========================================
    // 4. INTERAKSI KLIK (HANDLER DINAMIS)
    // =========================================

    const handler = new Cesium.ScreenSpaceEventHandler(viewer.scene.canvas);

    handler.setInputAction(function(movement) {
        const pickedFeature = viewer.scene.pick(movement.position);

        // Cek apakah yang diklik adalah bagian dari 3D Tiles
        if (Cesium.defined(pickedFeature) && pickedFeature instanceof Cesium.Cesium3DTileFeature) {

            // Ambil tileset induk dari fitur yang diklik
            const tileset = pickedFeature.primitive;

            // Cek: Apakah tileset ini punya link ke entity? (Lihat langkah B.4 di atas)
            if (tileset._linkedEntity) {
                viewer.selectedEntity = tileset._linkedEntity; // Buka Popup Entity
            }
        }
    }, Cesium.ScreenSpaceEventType.LEFT_CLICK);


    // =========================================
    // 5. FUNGSI PENCARIAN
    // =========================================

    window.cariObjek = function() {
        const input = document.getElementById('search-input');
        if(!input) return;

        const keyword = input.value.toLowerCase();
        if (!keyword) { alert("Masukkan kata kunci!"); return; }

        console.log("Mencari Ion Asset:", keyword);

        const entities = viewer.entities.values;
        let foundEntity = null;

        // Cari di daftar entity (yang sudah kita buat transparan tadi)
        for (let i = 0; i < entities.length; i++) {
            if (entities[i].name && entities[i].name.toLowerCase().includes(keyword)) {
                foundEntity = entities[i];
                break;
            }
        }

        if (foundEntity) {
            viewer.flyTo(foundEntity, {
                duration: 2.0,
                offset: new Cesium.HeadingPitchRange(
                    Cesium.Math.toRadians(0),
                    Cesium.Math.toRadians(-45),
                    300 // Jarak zoom
                )
            }).then(function() {
                viewer.selectedEntity = foundEntity;
            });
        } else {
            alert("Objek tidak ditemukan: " + keyword);
        }
    };

    // Event Listener Enter
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
