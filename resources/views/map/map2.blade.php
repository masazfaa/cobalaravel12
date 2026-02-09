@extends('map.layouts.master')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/map2.css') }}">

    <div id="map"></div>

    <div id="search-wrapper" class="leaflet-bar" style="position: absolute; top: 10px; left: 50px; z-index: 1000; display:none;"></div>
@endsection

@push('css')
    <style>
        /* Pastikan Peta punya tinggi */
        #map {
            width: 100%;
            height: 100vh; /* Full layar */
            z-index: 1;
        }
    </style>
@endpush

@push('js')
    <script>
        window.APP_URL = "{{ url('/') }}";
        window.GEOSERVER_LAYERS = @json($layers);
    </script>

    <script src="{{ asset('assets/js/map2.js') }}"></script>
@endpush
