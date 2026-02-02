    document.addEventListener('DOMContentLoaded', function () {

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


        // 1. TOKEN CESIUM
    Cesium.Ion.defaultAccessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJlZWY2NDhjNS00M2Y2LTQyOWItODRiMC02YzY0NmJiMGU4MWMiLCJpZCI6MTkwMjA3LCJpYXQiOjE3MDU0NzU1Nzh9.GC7NlIZjtNQXYctz51Kc71oWXspD4Gc4FQFyCM1TPYw';

    // 2. SETUP VIEWER (PEMBERSIHAN TOMBOL ATAS)
    const viewer = new Cesium.Viewer('cesiumContainer', {
        // Matikan semua kontrol kecuali Fullscreen
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

        // Sisakan hanya tombol Fullscreen
        fullscreenButton: true,

        // Matikan provider bawaan agar tidak berat
        terrainProvider: undefined,
        imageryProvider: false
    });

    // 3. FUNGSI UTAMA
    async function mulaiWebGIS() {
        try {
            viewer.entities.removeAll();

            // --- BAGIAN A: CITRA ---
            const layerCitra = new Cesium.UrlTemplateImageryProvider({
                url: window.APP_URL + "/citra/csrtftugm/{z}/{x}/{reverseY}.png",
                tilingScheme: new Cesium.WebMercatorTilingScheme(),
                minimumLevel: 12, maximumLevel: 21,
                rectangle: Cesium.Rectangle.fromDegrees(110.360, -7.780, 110.390, -7.750),
                hasAlphaChannel: true
            });
            layerCitra.errorEvent.addEventListener(e => e.retry = false);
            viewer.imageryLayers.addImageryProvider(layerCitra);

            // --- BAGIAN B: TERRAIN ---
            const terrainProvider = await Cesium.CesiumTerrainProvider.fromUrl(
                window.APP_URL + "/terrain/terrain_ftugm",
                { requestVertexNormals: true }
            );
            viewer.scene.setTerrain(new Cesium.Terrain(terrainProvider));

            // --- BAGIAN C: MODEL 3D (SGLC) ---
            const posisiSGLC = Cesium.Cartesian3.fromDegrees(110.372406, -7.765341, 0);

            viewer.entities.add({
                name: "Gedung SGLC UGM",
                description: `
                    <div style="padding: 10px;">
                        <p><b>Smart Green Learning Center</b></p>
                        <p>Ini adalah gedung baru SGLC di Fakultas Teknik UGM.</p>
                    </div>
                `,
                position: posisiSGLC,
                orientation: Cesium.Transforms.headingPitchRollQuaternion(
                    posisiSGLC,
                    new Cesium.HeadingPitchRoll(Cesium.Math.toRadians(90), 0, 0)
                ),
                model: {
                    uri: window.APP_URL + "/data3d/Smart Green Learning Center UGM.glb",
                    scale: 1.0,
                    heightReference: Cesium.HeightReference.CLAMP_TO_GROUND,
                    silhouetteColor: Cesium.Color.WHITE.withAlpha(0.5),
                    silhouetteSize: 0.0
                }
            });

            // --- BAGIAN D: KAMERA ---
            viewer.camera.flyTo({
                destination: Cesium.Cartesian3.fromDegrees(110.372480, -7.765125, 1000),
                orientation: {
                    heading: Cesium.Math.toRadians(0.0),
                    pitch: Cesium.Math.toRadians(-90.0),
                    roll: 0.0
                },
                duration: 3
            });

            // --- BAGIAN E: LOGIKA HIGHLIGHT ---
            viewer.selectedEntityChanged.addEventListener(function(entity) {
                if (Cesium.defined(entity) && entity.model) {
                    entity.model.silhouetteSize = 2.0;
                } else {
                    viewer.entities.values.forEach(function(ent) {
                        if (ent.model) ent.model.silhouetteSize = 0.0;
                    });
                }
            });

        } catch (error) {
            console.error(error);
        }
    }

    mulaiWebGIS();

});
