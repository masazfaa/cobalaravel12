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

    // Variabel untuk menyimpan Entity Popup
    let sglcEntity = null;


    // =========================================
    // 3. MEMUAT ASET (TERRAIN, BANGUNAN, POPUP)
    // =========================================

    try {
        // A. Load Terrain
        viewer.scene.setTerrain(
            new Cesium.Terrain(
                await Cesium.CesiumTerrainProvider.fromIonAssetId(2976593)
            )
        );

        // B. Load Bangunan (3D Tileset)
        const buildingTileset = await Cesium.Cesium3DTileset.fromIonAssetId(2976635);
        viewer.scene.primitives.add(buildingTileset);

        // C. Buat Entity Popup (Otomatis menempel di tengah bangunan)
        const assetCenter = buildingTileset.boundingSphere.center;

        sglcEntity = viewer.entities.add({
            id: 'sglc-entity',
            name: "Gedung SGLC UGM",
            position: assetCenter, // Posisi dinamis dari aset
            point: {
                pixelSize: 1,
                color: Cesium.Color.TRANSPARENT,
            },
            description: `
                <div style="padding: 10px;">
                    <p><b>Smart Green Learning Center</b></p>
                    <p>Ini adalah gedung baru SGLC di Fakultas Teknik UGM.</p>
                </div>
            `
        });

        // D. Atur Kamera Awal (Sesuai koordinat yang Anda minta)
        viewer.camera.setView({
            destination: Cesium.Cartesian3.fromDegrees(110.372480, -7.765125, 1000),
            orientation: {
                heading: Cesium.Math.toRadians(0.0),
                pitch: Cesium.Math.toRadians(-90.0),
                roll: 0.0
            }
        });

    } catch (error) {
        console.error("Gagal memuat aset Cesium:", error);
    }


    // =========================================
    // 4. INTERAKSI KLIK (HANDLER)
    // =========================================

    const handler = new Cesium.ScreenSpaceEventHandler(viewer.scene.canvas);

    handler.setInputAction(function(movement) {
        const pickedFeature = viewer.scene.pick(movement.position);

        // Jika user mengklik Bangunan Fisik (3D Tiles),
        // Kita paksa Viewer untuk menampilkan info dari Entity Popup kita
        if (Cesium.defined(pickedFeature) && pickedFeature instanceof Cesium.Cesium3DTileFeature) {
            if (sglcEntity) {
                viewer.selectedEntity = sglcEntity;
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
