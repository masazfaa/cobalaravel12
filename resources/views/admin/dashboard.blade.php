@extends('layouts.admin')

@section('title', 'Dashboard Admin - Manajemen Data')

@section('content')
<div class="row">
    <div class="col-sm-12">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">

                <ul class="nav nav-tabs border-bottom border-primary mb-4" id="mainTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-primary" id="non-geo-tab" data-bs-toggle="tab" data-bs-target="#non-geo" type="button" role="tab"><i class="mdi mdi-folder-outline me-1"></i> Non GeoServer</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-muted" id="geoserver-tab" data-bs-toggle="tab" data-bs-target="#geoserver" type="button" role="tab"><i class="mdi mdi-server-network me-1"></i> GeoServer</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-muted" id="cesium-self-tab" data-bs-toggle="tab" data-bs-target="#cesium-self" type="button" role="tab"><i class="mdi mdi-cube-outline me-1"></i> Cesium Self Hosted</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-muted" id="cesium-ion-tab" data-bs-toggle="tab" data-bs-target="#cesium-ion" type="button" role="tab"><i class="mdi mdi-cloud-outline me-1"></i> Cesium Ion</button>
                    </li>
                </ul>

                <div class="tab-content" id="mainTabContent">

                    <div class="tab-pane fade show active" id="non-geo" role="tabpanel">

                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn-sm" id="pills-admin-tab" data-bs-toggle="pill" data-bs-target="#pills-admin" type="button" role="tab">Batas Wilayah (Polygon)</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn-sm" id="pills-jalan-tab" data-bs-toggle="pill" data-bs-target="#pills-jalan" type="button" role="tab">Jaringan Jalan (Line)</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn-sm active" id="pills-masjid-tab" data-bs-toggle="pill" data-bs-target="#pills-masjid" type="button" role="tab">Titik Masjid (Point)</button>
                            </li>
                        </ul>

                        <div class="tab-content border-0 p-0 mt-4" id="pills-tabContent">

                            <div class="tab-pane fade" id="pills-admin" role="tabpanel">
                                <p class="text-muted"><i>Form CRUD Batas Wilayah (AdminKw) akan dibuat di sesi selanjutnya.</i></p>
                            </div>
                            <div class="tab-pane fade" id="pills-jalan" role="tabpanel">
                                <p class="text-muted"><i>Form CRUD Jaringan Jalan (JalanKw) akan dibuat di sesi selanjutnya.</i></p>
                            </div>

                            <div class="tab-pane fade show active" id="pills-masjid" role="tabpanel">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Data Titik Masjid</h5>
                                    <div>
                                        <button class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalTambahMasjid">
                                            <i class="mdi mdi-plus-circle me-1"></i> Tambah
                                        </button>
                                        <button class="btn btn-success btn-sm text-white mx-1" data-bs-toggle="modal" data-bs-target="#modalImportMasjid">
                                            <i class="mdi mdi-upload me-1"></i> Import
                                        </button>
                                        <a href="{{ route('masjid-kw.export') }}" class="btn btn-warning btn-sm text-dark">
                                            <i class="mdi mdi-download me-1"></i> Export GeoJSON
                                        </a>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Masjid</th>
                                                <th>Luas (m2)</th>
                                                <th>Jml Jamaah</th>
                                                <th>Takmir / Kontak</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($masjids as $index => $masjid)
                                            <tr>
                                                <td>{{ $masjids->firstItem() + $index }}</td>
                                                <td class="d-flex align-items-center">
                                                    @if($masjid->foto)
                                                        <img src="{{ asset($masjid->foto) }}" alt="Foto {{ $masjid->nama }}" class="rounded me-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded me-3 bg-secondary d-flex justify-content-center align-items-center text-white shadow-sm" style="width: 50px; height: 50px;">
                                                            <i class="mdi mdi-home-modern fs-4"></i>
                                                        </div>
                                                    @endif
                                                    <span class="fw-bold">{{ $masjid->nama }}</span>
                                                </td>
                                                <td>{{ $masjid->luas_m2 }}</td>
                                                <td>{{ $masjid->jumlah_jamaah }}</td>
                                                <td>{{ $masjid->takmir_cp ?? '-' }} <br> <small class="text-muted">{{ $masjid->no_telepon }}</small></td>
                                                <td class="text-center">

                                                    <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEditMasjid{{ $masjid->id }}">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>

                                                    <form action="{{ route('masjid-kw.destroy', $masjid->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus masjid ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i></button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="modalEditMasjid{{ $masjid->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg text-start">
                                                    <form action="{{ route('masjid-kw.update', $masjid->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Data Masjid</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="fw-bold small">Nama Masjid</label>
                                                                        <input type="text" name="nama" class="form-control" value="{{ $masjid->nama }}" required>
                                                                    </div>
                                                                    <div class="col-md-3 mb-3">
                                                                        <label class="fw-bold small">Luas (m2)</label>
                                                                        <input type="number" step="0.01" name="luas_m2" class="form-control" value="{{ $masjid->luas_m2 }}">
                                                                    </div>
                                                                    <div class="col-md-3 mb-3">
                                                                        <label class="fw-bold small">Jumlah Jamaah</label>
                                                                        <input type="number" name="jumlah_jamaah" class="form-control" value="{{ $masjid->jumlah_jamaah }}">
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="fw-bold small">Nama Takmir</label>
                                                                        <input type="text" name="takmir_cp" class="form-control" value="{{ $masjid->takmir_cp }}">
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="fw-bold small">No. Telepon / WA</label>
                                                                        <input type="text" name="no_telepon" class="form-control" value="{{ $masjid->no_telepon }}">
                                                                    </div>
                                                                    <div class="col-md-8 mb-3">
                                                                        <label class="fw-bold small">Ganti Foto <span class="text-muted fw-normal">(Opsional)</span></label>
                                                                        <input type="file" name="foto" class="form-control" accept="image/*">
                                                                        @if($masjid->foto)
                                                                            <small class="text-success"><i class="mdi mdi-check"></i> Sudah ada foto tersimpan.</small>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-md-4 mb-3">
                                                                        <label class="fw-bold small">Icon URL</label>
                                                                        <input type="text" name="icon_url" class="form-control" value="{{ $masjid->icon_url }}">
                                                                    </div>
                                                                </div>

                                                                <hr>
                                                                <p class="fw-bold mb-2">Data Geometri (Koordinat WGS84)</p>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="small text-muted">Longitude (X)</label>
                                                                        <input type="text" name="longitude" class="form-control" value="{{ $masjid->lng }}" required>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="small text-muted">Latitude (Y)</label>
                                                                        <input type="text" name="latitude" class="form-control" value="{{ $masjid->lat }}" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary btn-sm text-white">Update Data</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Belum ada data masjid. Silakan tambah atau import.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $masjids->appends(request()->except('masjid_page'))->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane fade" id="geoserver" role="tabpanel"><p class="p-3">Area GeoServer.</p></div>
                    <div class="tab-pane fade" id="cesium-self" role="tabpanel"><p class="p-3">Area Cesium Self Hosted.</p></div>
                    <div class="tab-pane fade" id="cesium-ion" role="tabpanel"><p class="p-3">Area Cesium Ion.</p></div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahMasjid" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('masjid-kw.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Masjid Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold small">Nama Masjid <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required placeholder="Contoh: Masjid Agung">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="fw-bold small">Luas (m2)</label>
                            <input type="number" step="0.01" name="luas_m2" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="fw-bold small">Jumlah Jamaah</label>
                            <input type="number" name="jumlah_jamaah" class="form-control" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold small">Nama Takmir</label>
                            <input type="text" name="takmir_cp" class="form-control" placeholder="Nama pengurus">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold small">No. Telepon / WA</label>
                            <input type="text" name="no_telepon" class="form-control" placeholder="0812...">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="fw-bold small">Upload Foto Masjid</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold small">Icon URL</label>
                            <input type="text" name="icon_url" class="form-control" value="./0.png">
                        </div>
                    </div>

                    <hr>
                    <p class="fw-bold mb-2">Data Geometri (Koordinat WGS84) <span class="text-danger">*</span></p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Longitude (X)</label>
                            <input type="text" name="longitude" class="form-control border-primary" required placeholder="110.xxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Latitude (Y)</label>
                            <input type="text" name="latitude" class="form-control border-primary" required placeholder="-7.xxx">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm text-white">Simpan Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalImportMasjid" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('masjid-kw.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import GeoJSON Masjid</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold mb-2">Pilih File (.geojson / .json)</label>
                        <input type="file" name="file_geojson" class="form-control" accept=".geojson, .json" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm text-white" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm text-white"><i class="mdi mdi-upload"></i> Proses Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
