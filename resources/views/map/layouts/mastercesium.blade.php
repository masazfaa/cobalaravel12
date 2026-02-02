<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Peta Laravel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.113/Build/Cesium/Widgets/widgets.css" rel="stylesheet">

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
</div>

<div id="search-wrapper"></div>
        @yield('content')
    </div>
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.113/Build/Cesium/Cesium.js"></script>

    @stack('js')

</body>
</html>
