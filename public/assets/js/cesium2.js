document.addEventListener('DOMContentLoaded', async function () {

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

    if (window.CESIUM_TOKEN) {
        Cesium.Ion.defaultAccessToken = window.CESIUM_TOKEN;
    } else {
        console.error("Token Cesium belum diset!");
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
        terrainProvider: undefined, // Kita set manual nanti
    });

    // Aktifkan Depth Test agar objek menyatu dengan terrain
    viewer.scene.globe.depthTestAgainstTerrain = true;

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

    // A. Load Terrain (Tetap satu kali saja)
    try {
        viewer.scene.setTerrain(
            new Cesium.Terrain(
                await Cesium.CesiumTerrainProvider.fromIonAssetId(2976593)
            )
        );
    } catch (e) { console.error("Terrain Error", e); }


    // B. CONFIG: Daftar Gedung (Simulasi Data dari Backend)
    // Cukup edit bagian ini kalau mau nambah/kurang gedung
    const daftarGedung = [
        {
            id: 2976635, // ID Aset di Cesium Ion
            name: "Gedung SGLC UGM",
            desc: "<p><b>SGLC UGM</b></p><p>Smart Green Learning Center.</p>"
        },
        {
            id: 2976596,
            name: "Gedung ERIC UGM",
            desc: "<p><b>ERIC UGM</b></p><p>Engineering Research and Innovation Center.</p>"
        }
        // Mau nambah gedung lagi? Tinggal tambah objek di sini.
    ];

    // C. LOOPING: Memuat semua gedung dalam daftar
    for (const data of daftarGedung) {
        try {
            // 1. Load Tileset
            const tileset = await Cesium.Cesium3DTileset.fromIonAssetId(data.id);
            viewer.scene.primitives.add(tileset);

            // 2. Buat Entity Popup
            const center = tileset.boundingSphere.center;
            const entity = viewer.entities.add({
                name: data.name,
                position: center,
                point: { pixelSize: 1, color: Cesium.Color.TRANSPARENT },
                description: `<div style="padding:10px">${data.desc}</div>`
            });

            // 3. RAHASIA DINAMIS: Tempelkan Entity ke Tileset
            // Kita simpan referensi entity di dalam objek tileset itu sendiri
            tileset._linkedEntity = entity;

        } catch (error) {
            console.error(`Gagal memuat gedung ${data.name}:`, error);
        }
    }


    // =========================================
    // 4. INTERAKSI KLIK (HANDLER DINAMIS)
    // =========================================
    // Handler ini tidak perlu diedit lagi walau gedungnya ada 1000

    const handler = new Cesium.ScreenSpaceEventHandler(viewer.scene.canvas);

    handler.setInputAction(function(movement) {
        const pickedFeature = viewer.scene.pick(movement.position);

        if (Cesium.defined(pickedFeature) && pickedFeature instanceof Cesium.Cesium3DTileFeature) {

            // Ambil tileset induk dari fitur yang diklik
            const tileset = pickedFeature.primitive;

            // Cek: Apakah tileset ini punya link ke entity? (Lihat langkah C.3 di atas)
            if (tileset._linkedEntity) {
                viewer.selectedEntity = tileset._linkedEntity; // Buka Popup
            }
        }

    }, Cesium.ScreenSpaceEventType.LEFT_CLICK);


    // =========================================
    // 5. FUNGSI PENCARIAN
    // =========================================

    window.cariObjek = function() {
        const keyword = document.getElementById('search-input').value.toLowerCase();
        if (!keyword) { alert("Masukkan kata kunci!"); return; }

        const entities = viewer.entities.values;
        let foundEntity = null;

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

    document.getElementById('search-input').addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            window.cariObjek();
        }
    });

});
