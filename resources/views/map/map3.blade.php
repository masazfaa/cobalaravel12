@extends('map.layouts.mastercesium')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/cesium.css') }}">
<script src="{{ asset('assets/js/cesium.js') }}"></script>
    <div id="cesiumContainer"></div>
@endsection

@push('css')

@endpush

@push('js')
<script>
    window.APP_URL = "{{ url('/') }}";
    window.CESIUM_TOKEN = "{{ env('CESIUM_TOKEN') }}";
    window.DATA_GEDUNG_RAW = @json($dataSelfHosted);
</script>
@endpush
