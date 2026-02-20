@extends('map.layouts.master')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/map1.css') }}">

    {{-- Container Peta --}}
    <div id="map"></div>

    {{-- Container Tombol Search (Sesuai JS kamu ada getElementById search-wrapper) --}}
    <div id="search-wrapper" style="position: absolute; top: 10px; left: 50px; z-index: 1000;"></div>
@endsection

@push('js')
<script>
    window.DATA_ADMIN = @json($adminGeoJson);
    window.DATA_JALAN = @json($jalanGeoJson);
    window.DATA_MASJID = @json($masjidGeoJson);

    console.log("Data Admin Loaded:", window.DATA_ADMIN);
    console.log("Data Jalan Loaded:", window.DATA_JALAN);
    console.log("Data Masjid Loaded:", window.DATA_MASJID);
</script>

{{-- Panggil JS Map setelah Data didefinisikan --}}
<script src="{{ asset('assets/js/map1.js') }}"></script>
@endpush
