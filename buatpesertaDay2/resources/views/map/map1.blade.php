@extends('map.layouts.master')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/map1.css') }}">
    <script src="{{ asset('assets/js/map1.js') }}"></script>

    <div id="map"></div>

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

@endsection
<style>

</style>
@push('css')

@endpush

@push('js')
<script>

</script>
@endpush
