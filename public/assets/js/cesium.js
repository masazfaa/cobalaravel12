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
    if (window.CESIUM_TOKEN) {
            Cesium.Ion.defaultAccessToken = window.CESIUM_TOKEN;
        } else {
            console.error("Token Cesium belum diset di .env!");
        }

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
        fullscreenButton: true,
        terrainProvider: Cesium.Terrain.fromWorldTerrain(),
        // imageryProvider: false
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
            viewer.camera.setView({
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

    // --- FUNGSI PENCARIAN OBJEK 3D ---
window.cariObjek = function() {
    const keyword = document.getElementById('search-input').value.toLowerCase();

    if (!keyword) {
        alert("Masukkan kata kunci pencarian!");
        return;
    }

    console.log("Mencari: " + keyword);

    // 1. Ambil semua entity yang ada di viewer
    const entities = viewer.entities.values;
    let foundEntity = null;

    // 2. Loop cari yang namanya cocok
    for (let i = 0; i < entities.length; i++) {
        const entity = entities[i];

        // Cek Nama (name)
        const nameMatch = entity.name && entity.name.toLowerCase().includes(keyword);

        // (Opsional) Cek Deskripsi juga kalau mau lebih canggih
        // const descMatch = entity.description && entity.description.getValue().toLowerCase().includes(keyword);

        if (nameMatch) {
            foundEntity = entity;
            break; // Ketemu satu, langsung berhenti (atau bisa dibuat list jika mau)
        }
    }

    // 3. Aksi jika ditemukan
    if (foundEntity) {
        // A. Terbang ke objek (Zoom Dinamis sesuai ukuran objek)
        viewer.flyTo(foundEntity, {
            duration: 2.0, // Durasi terbang 2 detik
            offset: new Cesium.HeadingPitchRange(
                Cesium.Math.toRadians(0), // Heading
                Cesium.Math.toRadians(-45), // Pitch (Agak menunduk)
                0 // Range 0 = Cesium otomatis hitung jarak ideal biar pas satu layar
            )
        }).then(function() {
            // B. Buka Popup (InfoBox) Otomatis setelah sampai
            viewer.selectedEntity = foundEntity;
        });

        // C. (Opsional) Efek Highlight Putih Aktif
        if(foundEntity.model) {
            foundEntity.model.silhouetteSize = 2.0;
        }

    } else {
        alert("Objek tidak ditemukan dengan kata kunci: " + keyword);
    }
};

// Tambahan: Biar bisa tekan ENTER di keyboard
document.getElementById('search-input').addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        window.cariObjek();
    }
});

});
