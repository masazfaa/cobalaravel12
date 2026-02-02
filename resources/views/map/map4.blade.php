@extends('map.layouts.mastercesium')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/cesium2.css') }}">
<script src="{{ asset('assets/js/cesium2.js') }}"></script>
    <div id="cesiumContainer"></div>
@endsection

@push('css')

@endpush

@push('js')
<script>
    window.APP_URL = "{{ url('/') }}";
    window.CESIUM_TOKEN = "{{ env('CESIUM_TOKEN') }}";
</script>
@endpush
