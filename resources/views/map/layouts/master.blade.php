<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Peta Laravel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/3.0.9/leaflet-search.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-panel-layers@1.3.0/dist/leaflet-panel-layers.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    @stack('css')
</head>
<body>

    <div class="container-fluid p-0">
        <div id="header-box" onclick="toggleMenu()">
        <img src="{{ asset('GAS.png') }}" class="logo-img">

        <div class="title-container">
            <div class="app-title">WEBGIS KOTA X</div>
            <div class="app-subtitle">Ini adalah subtitle</div>
        </div>

        <i class="fa-solid fa-caret-down" id="menu-icon"></i>
    </div>

    <div id="menu-dropdown">
        <div class="menu-header">MENU UTAMA</div>

        <a href="{{ url('login') }}" class="menu-item">
            <i class="fa-solid fa-lock"></i> Login Admin
        </a>

        <a href="{{ url('geoserver')}}" class="menu-item">
            <i></i> Versi Geoserver
        </a>

        <a href="{{ url('cesium')}}" class="menu-item">
            <i></i> Peta 3D Cesium
        </a>
        <a href="{{ url('cesiumion')}}" class="menu-item">
        <i></i> Peta 3D Cesium Ion
        </a>
    </div>

    <div id="search-wrapper"></div>

    <div id="coord-box">
        <div class="coord-item">
            <span class="coord-label">Lat:</span>
            <span id="lat-val" class="coord-num">-</span>
        </div>
        <div class="coord-divider"></div>
        <div class="coord-item">
            <span class="coord-label">Lon:</span>
            <span id="lng-val" class="coord-num">-</span>
        </div>
    </div>
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/3.0.9/leaflet-search.min.js"></script>
     <script src="https://unpkg.com/leaflet-panel-layers@1.3.0/dist/leaflet-panel-layers.min.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
    @stack('js')

</body>
</html>
