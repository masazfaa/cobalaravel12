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


            // --- BAGIAN B: LOOPING MEMUAT GEDUNG DARI DATABASE (CESIUM ION) ---

            // 1. MAPPING: Siapkan data agar formatnya rapi
            const daftarTileset = RAW_DATA.map(function(item) {
                return {
                    id: item.ion_asset_id, // ID Kunci dari Cesium Cloud
                    name: item.name,
                    // Rakit HTML untuk Popup (Sama persis dengan gaya Self-Hosted)
                    desc: `
                        <div style="padding:10px">
                            <p><b>${item.name}</b></p>
                            <div>${item.description || '-'}</div>
                        </div>
                    `
                };
            });

            console.log(`✅ Memuat ${daftarTileset.length} aset Cesium Ion.`);

            // 2. LOOPING: Eksekusi muat ke Peta
            // Kita pakai async di dalam forEach karena Cesium Ion butuh loading internet
            daftarTileset.forEach(async (data) => {

                // Validasi sederhana
                if (!data.id) {
                    console.warn(`Data ${data.name} dilewati karena tidak punya ID Aset.`);
                    return;
                }

                try {
                    // a. Panggil Aset dari Cloud
                    const tileset = await Cesium.Cesium3DTileset.fromIonAssetId(data.id);
                    viewer.scene.primitives.add(tileset);

                    // b. Trik Zoom & Titik Tengah (Tunggu sampai aset siap/ready)
                    await tileset.readyPromise;
                    const center = tileset.boundingSphere.center;

                    // c. Buat Entity "Bayangan" (Titik Transparan di tengah aset)
                    // Tujuannya agar Tileset bisa diklik dan muncul popup (karena Tileset aslinya gak punya popup)
                    const entity = viewer.entities.add({
                        name: data.name,
                        position: center,
                        point: {
                            pixelSize: 10,
                            color: Cesium.Color.TRANSPARENT // Titik tidak terlihat
                        },
                        description: data.desc // Ambil dari hasil mapping di atas
                    });

                    // d. Metadata Linking (Agar saat gedung diklik, popup entity yang muncul)
                    tileset._linkedEntity = entity;
                    tileset._name = data.name;

                    console.log(`✅ Sukses: ${data.name}`);

                } catch (error) {
                    console.error(`❌ Gagal: ${data.name}`, error);
                }
            });


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
