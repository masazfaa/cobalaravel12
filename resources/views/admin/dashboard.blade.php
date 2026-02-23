@extends('layouts.admin')

@section('title', 'Dashboard Admin - Manajemen Data')

@section('content')
        <div class="home-tab mt-2">
            <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active ps-0" id="non-geoserver-tab" data-bs-toggle="tab" href="#non-geoserver" role="tab" aria-controls="non-geoserver" aria-selected="true">
                            <i class="mdi mdi-folder-outline me-1"></i> Non GeoServer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="geoserver-tab" data-bs-toggle="tab" href="#geoserver" role="tab" aria-controls="geoserver" aria-selected="false">
                            <i class="mdi mdi-server-network me-1"></i> GeoServer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="cesium-self-tab" data-bs-toggle="tab" href="#cesium-self" role="tab" aria-controls="cesium-self" aria-selected="false">
                            <i class="mdi mdi-cube-outline me-1"></i> Cesium Self Hosted
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border-0" id="cesium-ion-tab" data-bs-toggle="tab" href="#cesium-ion" role="tab" aria-controls="cesium-ion" aria-selected="false">
                            <i class="mdi mdi-cloud-outline me-1"></i> Cesium Ion
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content tab-content-basic mt-4">

                <div class="tab-pane fade show active" id="non-geoserver" role="tabpanel" aria-labelledby="non-geoserver-tab">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title">Data Spasial Non GeoServer</h4>
                            <p class="card-description">Area ini disiapkan untuk manajemen data spasial format mentah (GeoJSON, KML, dll).</p>
                            <div class="text-center py-5 text-muted">
                                <i class="mdi mdi-folder-plus-outline mdi-48px mb-2 text-primary"></i>
                                <h5>Belum ada data</h5>
                                <p>Silakan siapkan tabel CRUD untuk kategori ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="geoserver" role="tabpanel" aria-labelledby="geoserver-tab">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title">Koneksi Data GeoServer</h4>
                            <p class="card-description">Area ini disiapkan untuk manajemen data WMS/WFS dari GeoServer.</p>
                            <div class="text-center py-5 text-muted">
                                <i class="mdi mdi-server-network mdi-48px mb-2 text-success"></i>
                                <h5>Belum ada data</h5>
                                <p>Silakan siapkan tabel CRUD untuk kategori ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="cesium-self" role="tabpanel" aria-labelledby="cesium-self-tab">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title">Data 3D Cesium (Self Hosted)</h4>
                            <p class="card-description">Area ini disiapkan untuk 3D Tiles / Terrain yang di-host mandiri.</p>
                            <div class="text-center py-5 text-muted">
                                <i class="mdi mdi-cube-outline mdi-48px mb-2 text-warning"></i>
                                <h5>Belum ada data</h5>
                                <p>Silakan siapkan tabel CRUD untuk kategori ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="cesium-ion" role="tabpanel" aria-labelledby="cesium-ion-tab">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title">Asset Cesium Ion</h4>
                            <p class="card-description">Area ini disiapkan untuk manajemen Asset ID dari akun Cesium Ion.</p>
                            <div class="text-center py-5 text-muted">
                                <i class="mdi mdi-cloud-outline mdi-48px mb-2 text-info"></i>
                                <h5>Belum ada data</h5>
                                <p>Silakan siapkan tabel CRUD untuk kategori ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
