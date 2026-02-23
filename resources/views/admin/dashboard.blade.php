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
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
                                    <h5 class="card-title mb-0">Batas Wilayah (Admin)</h5>
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <form action="{{ route('dashboard') }}" method="GET" class="d-flex">
                                            <input type="text" name="search_admin" class="form-control form-control-sm border-primary me-1" placeholder="Cari Padukuhan..." value="{{ request('search_admin') }}">
                                            <button type="submit" class="btn btn-primary btn-sm text-white"><i class="mdi mdi-magnify"></i></button>
                                            @if(request('search_admin'))
                                                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm text-white ms-1"><i class="mdi mdi-close"></i></a>
                                            @endif
                                        </form>

                                        <button class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin"><i class="mdi mdi-plus-circle me-1"></i> Tambah</button>
                                        <button class="btn btn-success btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalImportAdmin"><i class="mdi mdi-upload me-1"></i> Import</button>
                                        <a href="{{ route('admin-kw.export') }}" class="btn btn-warning btn-sm text-dark"><i class="mdi mdi-download me-1"></i> Export</a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Padukuhan</th>
                                                <th>Kalurahan</th>
                                                <th>Luas (Ha)</th>
                                                <th>Jml KK</th>
                                                <th>Penduduk</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($admins as $admin)
                                            <tr>
                                                <td class="fw-bold">{{ $admin->padukuhan }}</td>
                                                <td>{{ $admin->kalurahan }}</td>
                                                <td>{{ $admin->luas }}</td>
                                                <td>{{ $admin->jumlah_kk }}</td>
                                                <td>L: {{ $admin->jumlah_laki }} | P: {{ $admin->jumlah_perempuan }} <br> Total: <b>{{ $admin->jumlah_penduduk }}</b></td>
                                                <td class="text-center">
                                                    <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEditAdmin{{ $admin->id }}">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('admin-kw.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus batas wilayah ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i></button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="modalEditAdmin{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg text-start">
                                                    <form action="{{ route('admin-kw.update', $admin->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-content">
                                                            <div class="modal-header"><h5 class="modal-title">Edit Batas Wilayah</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-2"><label class="small fw-bold">Kalurahan</label><input type="text" name="kalurahan" class="form-control" value="{{ $admin->kalurahan }}"></div>
                                                                    <div class="col-md-6 mb-2"><label class="small fw-bold">Padukuhan</label><input type="text" name="padukuhan" class="form-control" value="{{ $admin->padukuhan }}"></div>
                                                                    <div class="col-md-3 mb-2"><label class="small fw-bold">Luas (Ha)</label><input type="number" step="any" name="luas" class="form-control" value="{{ $admin->luas }}"></div>
                                                                    <div class="col-md-3 mb-2"><label class="small fw-bold">Jml KK</label><input type="number" name="jumlah_kk" class="form-control" value="{{ $admin->jumlah_kk }}"></div>

                                                                    <div class="col-md-2 mb-2"><label class="small fw-bold">Jml Pddk</label><input type="number" name="jumlah_penduduk" class="form-control" value="{{ $admin->jumlah_penduduk }}"></div>

                                                                    <div class="col-md-2 mb-2"><label class="small fw-bold">Jml Laki2</label><input type="number" name="jumlah_laki" class="form-control" value="{{ $admin->jumlah_laki }}"></div>
                                                                    <div class="col-md-2 mb-2"><label class="small fw-bold">Jml Peremp</label><input type="number" name="jumlah_perempuan" class="form-control" value="{{ $admin->jumlah_perempuan }}"></div>
                                                                    <div class="col-md-12 mb-2">
                                                                        <label class="small fw-bold">GeoJSON Geometry <span class="text-danger">*</span></label>
                                                                        <textarea name="geom" class="form-control font-monospace" rows="4" required>{{ $admin->geom_json }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer"><button type="submit" class="btn btn-primary btn-sm text-white">Update Data</button></div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            @empty
                                            <tr><td colspan="6" class="text-center py-3">Belum ada data Batas Wilayah.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2">{{ $admins->appends(request()->except('admin_page'))->links('pagination::bootstrap-5') }}</div>
                            </div>

                            <div class="tab-pane fade" id="pills-jalan" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
                                    <h5 class="card-title mb-0">Jaringan Jalan</h5>
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <form action="{{ route('dashboard') }}" method="GET" class="d-flex">
                                            <input type="text" name="search_jalan" class="form-control form-control-sm border-primary me-1" placeholder="Cari Nama Jalan..." value="{{ request('search_jalan') }}">
                                            <button type="submit" class="btn btn-primary btn-sm text-white"><i class="mdi mdi-magnify"></i></button>
                                            @if(request('search_jalan'))
                                                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm text-white ms-1"><i class="mdi mdi-close"></i></a>
                                            @endif
                                        </form>

                                        <button class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalTambahJalan"><i class="mdi mdi-plus-circle me-1"></i> Tambah</button>
                                        <button class="btn btn-success btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalImportJalan"><i class="mdi mdi-upload me-1"></i> Import</button>
                                        <a href="{{ route('jalan-kw.export') }}" class="btn btn-warning btn-sm text-dark"><i class="mdi mdi-download me-1"></i> Export</a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Jalan</th>
                                                <th>Panjang (m)</th>
                                                <th>Lebar (m)</th>
                                                <th>Kondisi</th>
                                                <th>Kewenangan</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($jalans as $jalan)
                                            <tr>
                                                <td class="fw-bold">{{ $jalan->nama }}</td>
                                                <td>{{ $jalan->panjang }}</td>
                                                <td>{{ $jalan->lebar }}</td>
                                                <td>{{ $jalan->kondisi }}</td>
                                                <td>{{ $jalan->kewenangan }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEditJalan{{ $jalan->id }}">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('jalan-kw.destroy', $jalan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jalan ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i></button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="modalEditJalan{{ $jalan->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg text-start">
                                                    <form action="{{ route('jalan-kw.update', $jalan->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-content">
                                                            <div class="modal-header"><h5 class="modal-title">Edit Jaringan Jalan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-2"><label class="small fw-bold">Nama Jalan</label><input type="text" name="nama" class="form-control" value="{{ $jalan->nama }}"></div>
                                                                    <div class="col-md-3 mb-2"><label class="small fw-bold">Panjang (m)</label><input type="number" step="any" name="panjang" class="form-control" value="{{ $jalan->panjang }}"></div>
                                                                    <div class="col-md-3 mb-2"><label class="small fw-bold">Lebar (m)</label><input type="number" step="any" name="lebar" class="form-control" value="{{ $jalan->lebar }}"></div>
                                                                    <div class="col-md-4 mb-2"><label class="small fw-bold">Kondisi</label><input type="text" name="kondisi" class="form-control" value="{{ $jalan->kondisi }}"></div>
                                                                    <div class="col-md-4 mb-2"><label class="small fw-bold">Kewenangan</label><input type="text" name="kewenangan" class="form-control" value="{{ $jalan->kewenangan }}"></div>
                                                                    <div class="col-md-4 mb-2"><label class="small fw-bold">Status</label><input type="text" name="status" class="form-control" value="{{ $jalan->status }}"></div>
                                                                    <div class="col-md-12 mb-2">
                                                                        <label class="small fw-bold">GeoJSON Geometry <span class="text-danger">*</span></label>
                                                                        <textarea name="geom" class="form-control font-monospace" rows="4" required>{{ $jalan->geom_json }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer"><button type="submit" class="btn btn-primary btn-sm text-white">Update Data</button></div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            @empty
                                            <tr><td colspan="6" class="text-center py-3">Belum ada data Jaringan Jalan.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2">{{ $jalans->appends(request()->except('jalan_page'))->links('pagination::bootstrap-5') }}</div>
                            </div>

                            <div class="tab-pane fade show active" id="pills-masjid" role="tabpanel">

                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
                                    <h5 class="card-title mb-0">Data Titik Masjid</h5>
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <form action="{{ route('dashboard') }}" method="GET" class="d-flex">
                                            <input type="text" name="search_masjid" class="form-control form-control-sm border-primary me-1" placeholder="Cari Masjid..." value="{{ request('search_masjid') }}">
                                            <button type="submit" class="btn btn-primary btn-sm text-white"><i class="mdi mdi-magnify"></i></button>
                                            @if(request('search_masjid'))
                                                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm text-white ms-1"><i class="mdi mdi-close"></i></a>
                                            @endif
                                        </form>

                                        <button class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalTambahMasjid"><i class="mdi mdi-plus-circle me-1"></i> Tambah</button>
                                        <button class="btn btn-success btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalImportMasjid"><i class="mdi mdi-upload me-1"></i> Import</button>
                                        <a href="{{ route('masjid-kw.export') }}" class="btn btn-warning btn-sm text-dark"><i class="mdi mdi-download me-1"></i> Export</a>
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

                    <div class="tab-pane fade" id="geoserver" role="tabpanel">

                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2 mt-3">
                            <div>
                                <h5 class="card-title mb-1">Manajemen Layer GeoServer</h5>
                                <p class="text-muted small mb-0">Atur endpoint WMS/WFS untuk ditampilkan di peta.</p>
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <form action="{{ route('dashboard') }}" method="GET" class="d-flex">
                                    <input type="text" name="search_geoserver" class="form-control form-control-sm border-primary me-1" placeholder="Cari Layer..." value="{{ request('search_geoserver') }}">
                                    <button type="submit" class="btn btn-primary btn-sm text-white"><i class="mdi mdi-magnify"></i></button>
                                    @if(request('search_geoserver'))
                                        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm text-white ms-1"><i class="mdi mdi-close"></i></a>
                                    @endif
                                </form>
                                <button class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalTambahGeo">
                                    <i class="mdi mdi-plus-circle me-1"></i> Tambah Layer
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Layer (Workspace:Name)</th>
                                        <th class="text-start">Judul Tampilan (Title)</th>
                                        <th>Tipe</th>
                                        <th>Layanan Aktif</th>
                                        <th>Status Peta</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($geoservers as $geo)
                                    <tr>
                                        <td class="text-center font-monospace"><span class="badge bg-light text-dark border">{{ $geo->workspace }}:{{ $geo->layer_name }}</span></td>
                                        <td class="fw-bold">{{ $geo->title }}</td>
                                        <td class="text-center">{{ strtoupper($geo->type) }}</td>
                                        <td class="text-center">
                                            @if($geo->enable_wms) <span class="badge bg-info text-white" title="Visualisasi Gambar">WMS</span> @endif
                                            @if($geo->enable_wfs) <span class="badge bg-warning text-dark" title="Interaktif/Pencarian">WFS</span> @endif
                                            @if($geo->enable_wmts) <span class="badge bg-success text-white" title="Raster Cepat">WMTS</span> @endif
                                            @if(!$geo->enable_wms && !$geo->enable_wfs && !$geo->enable_wmts) <span class="text-muted small">-Tidak ada-</span> @endif
                                        </td>
                                        <td class="text-center">
                                            @if($geo->is_active)
                                                <span class="badge rounded-pill bg-success"><i class="mdi mdi-check-circle me-1"></i>ON</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary"><i class="mdi mdi-minus-circle me-1"></i>OFF</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEditGeo{{ $geo->id }}"><i class="mdi mdi-pencil"></i></button>
                                            <form action="{{ route('geoserver.destroy', $geo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus layer ini? Konfigurasi di peta akan hilang!');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i></button>
                                            </form>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalEditGeo{{ $geo->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <form action="{{ route('geoserver.update', $geo->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title"><i class="mdi mdi-pencil-box me-2"></i>Edit Layer GeoServer</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body bg-light">
                                                        <div class="card shadow-sm mb-3">
                                                            <div class="card-body p-3">
                                                                <h6 class="card-subtitle mb-3 text-muted fw-bold">1. Identitas Layer</h6>
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label small fw-bold">Workspace <span class="text-danger">*</span></label>
                                                                        <input type="text" name="workspace" class="form-control font-monospace" value="{{ $geo->workspace }}" required>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label small fw-bold">Nama Layer (Store Name) <span class="text-danger">*</span></label>
                                                                        <input type="text" name="layer_name" class="form-control font-monospace" value="{{ $geo->layer_name }}" required>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="form-label small fw-bold">Judul Tampilan (Title Peta) <span class="text-danger">*</span></label>
                                                                        <input type="text" name="title" class="form-control fw-bold" value="{{ $geo->title }}" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card shadow-sm mb-3">
                                                            <div class="card-body p-3">
                                                                <h6 class="card-subtitle mb-3 text-muted fw-bold">2. Konfigurasi Server</h6>
                                                                <div class="row g-3 mb-2">
                                                                    <div class="col-12">
                                                                        <label class="form-label small fw-bold">Base URL GeoServer <span class="text-danger">*</span></label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text bg-light"><i class="mdi mdi-server"></i></span>
                                                                            <input type="url" name="base_url" class="form-control font-monospace" value="{{ $geo->base_url }}" required>
                                                                        </div>
                                                                        <div class="form-text small">Contoh: http://localhost:8080/geoserver/ (Akhiri dengan slash '/')</div>
                                                                    </div>
                                                                </div>
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label small fw-bold">Tipe Data</label>
                                                                        <select name="type" class="form-select">
                                                                            <option value="vector" {{ $geo->type == 'vector' ? 'selected' : '' }}>Vector (Garis/Poligon/Titik)</option>
                                                                            <option value="raster" {{ $geo->type == 'raster' ? 'selected' : '' }}>Raster (Citra/Foto Udara)</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label small fw-bold">Z-Index (Tumpukan)</label>
                                                                        <input type="number" name="z_index" class="form-control" value="{{ $geo->z_index }}" min="0">
                                                                        <div class="form-text small">Semakin besar angkanya, semakin di atas posisinya.</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card shadow-sm border-primary">
                                                            <div class="card-body p-3">
                                                                <h6 class="card-subtitle mb-3 text-primary fw-bold">3. Layanan & Status Peta</h6>
                                                                <ul class="list-group list-group-flush">
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light ps-0">
                                                                        <div>
                                                                            <span class="fw-bold d-block"><i class="mdi mdi-eye me-1 text-info"></i> Enable WMS</span>
                                                                            <small class="text-muted">Aktifkan visualisasi gambar (wajib untuk tampil).</small>
                                                                        </div>
                                                                        <div class="form-check form-switch mb-0">
                                                                            <input class="form-check-input" type="checkbox" name="enable_wms" value="1" {{ $geo->enable_wms ? 'checked' : '' }} style="transform: scale(1.3);">
                                                                        </div>
                                                                    </li>
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light ps-0">
                                                                        <div>
                                                                            <span class="fw-bold d-block"><i class="mdi mdi-cursor-default-click me-1 text-warning"></i> Enable WFS</span>
                                                                            <small class="text-muted">Aktifkan fitur klik info dan pencarian (hanya vektor).</small>
                                                                        </div>
                                                                        <div class="form-check form-switch mb-0">
                                                                            <input class="form-check-input" type="checkbox" name="enable_wfs" value="1" {{ $geo->enable_wfs ? 'checked' : '' }} style="transform: scale(1.3);">
                                                                        </div>
                                                                    </li>
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light ps-0">
                                                                        <div>
                                                                            <span class="fw-bold d-block"><i class="mdi mdi-image-filter-hdr me-1 text-success"></i> Enable WMTS</span>
                                                                            <small class="text-muted">Gunakan GeoWebCache untuk loading raster cepat.</small>
                                                                        </div>
                                                                        <div class="form-check form-switch mb-0">
                                                                            <input class="form-check-input" type="checkbox" name="enable_wmts" value="1" {{ $geo->enable_wmts ? 'checked' : '' }} style="transform: scale(1.3);">
                                                                        </div>
                                                                    </li>
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-success bg-opacity-10 ps-2 rounded mt-2">
                                                                        <div>
                                                                            <span class="fw-bold d-block text-success"><i class="mdi mdi-power me-1"></i> STATUS DEFAULT PETA</span>
                                                                            <small class="text-dark">Apakah layer langsung tampil saat peta dibuka?</small>
                                                                        </div>
                                                                        <div class="form-check form-switch mb-0">
                                                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $geo->is_active ? 'checked' : '' }} style="transform: scale(1.4);">
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                    </div> <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-info btn-sm text-white">Simpan Perubahan</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada konfigurasi layer GeoServer. Klik "Tambah Layer" untuk memulai.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">{{ $geoservers->appends(request()->except('geoserver_page'))->links('pagination::bootstrap-5') }}</div>
                    </div>

                    <div class="tab-pane fade" id="cesium-self" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2 mt-3">
                            <div><h5 class="card-title mb-1">Manajemen Model 3D Lokal</h5><p class="text-muted small mb-0">Atur posisi model glTF/GLB di peta.</p></div>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <form action="{{ route('dashboard') }}" method="GET" class="d-flex">
                                    <input type="text" name="search_self" class="form-control form-control-sm border-primary me-1" placeholder="Cari Model..." value="{{ request('search_self') }}">
                                    <button type="submit" class="btn btn-primary btn-sm text-white"><i class="mdi mdi-magnify"></i></button>
                                    @if(request('search_self')) <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm text-white ms-1"><i class="mdi mdi-close"></i></a> @endif
                                </form>
                                <button class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalTambahSelf"><i class="mdi mdi-plus-circle me-1"></i> Tambah Model</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm align-middle">
                                <thead class="table-light"><tr><th>Nama Bangunan</th><th>Path / URL (.gltf)</th><th>Koordinat (Lng, Lat)</th><th>Tinggi & Arah</th><th class="text-center">Aksi</th></tr></thead>
                                <tbody>
                                    @forelse($selfHosteds as $self)
                                    <tr>
                                        <td class="fw-bold">{{ $self->name }}</td>
                                        <td class="font-monospace text-muted small">{{ $self->model_path }}</td>
                                        <td>{{ $self->longitude }}, {{ $self->latitude }}</td>
                                        <td>H: {{ $self->height }}m | Rot: {{ $self->heading }}&deg;</td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEditSelf{{ $self->id }}"><i class="mdi mdi-pencil"></i></button>
                                            <form action="{{ route('cesium-self.destroy', $self->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus model 3D ini?');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i></button></form>
                                        </td>
                                    </tr>
                                    @empty <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada model 3D lokal.</td></tr> @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">{{ $selfHosteds->appends(request()->except('self_page'))->links('pagination::bootstrap-5') }}</div>
                    </div>

                    <div class="tab-pane fade" id="cesium-ion" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2 mt-3">
                            <div><h5 class="card-title mb-1">Manajemen Asset Cesium Ion</h5><p class="text-muted small mb-0">Panggil 3D Tiles/Photogrammetry dari Cloud.</p></div>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <form action="{{ route('dashboard') }}" method="GET" class="d-flex">
                                    <input type="text" name="search_ion" class="form-control form-control-sm border-primary me-1" placeholder="Cari Asset..." value="{{ request('search_ion') }}">
                                    <button type="submit" class="btn btn-primary btn-sm text-white"><i class="mdi mdi-magnify"></i></button>
                                    @if(request('search_ion')) <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm text-white ms-1"><i class="mdi mdi-close"></i></a> @endif
                                </form>
                                <button class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalTambahIon"><i class="mdi mdi-plus-circle me-1"></i> Tambah Asset</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm align-middle">
                                <thead class="table-light"><tr><th>Nama Lokasi / Asset</th><th>Ion Asset ID</th><th>Deskripsi</th><th class="text-center">Aksi</th></tr></thead>
                                <tbody>
                                    @forelse($ions as $ion)
                                    <tr>
                                        <td class="fw-bold">{{ $ion->name }}</td>
                                        <td><span class="badge bg-primary fs-6">{{ $ion->ion_asset_id }}</span></td>
                                        <td>{{ Str::limit($ion->description, 50) }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEditIon{{ $ion->id }}"><i class="mdi mdi-pencil"></i></button>
                                            <form action="{{ route('cesium-ion.destroy', $ion->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus Asset Ion ini?');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm text-white"><i class="mdi mdi-delete"></i></button></form>
                                        </td>
                                    </tr>
                                    @empty <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada asset Ion.</td></tr> @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">{{ $ions->appends(request()->except('ion_page'))->links('pagination::bootstrap-5') }}</div>
                    </div>
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

<div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg text-start">
        <form action="{{ route('admin-kw.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Batas Wilayah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2"><label class="small fw-bold">Kalurahan</label><input type="text" name="kalurahan" class="form-control"></div>
                        <div class="col-md-6 mb-2"><label class="small fw-bold">Padukuhan</label><input type="text" name="padukuhan" class="form-control"></div>
                        <div class="col-md-3 mb-2"><label class="small fw-bold">Luas (Ha)</label><input type="number" step="any" name="luas" class="form-control" value="0"></div>
                        <div class="col-md-3 mb-2"><label class="small fw-bold">Jml KK</label><input type="number" name="jumlah_kk" class="form-control" value="0"></div>

                        <div class="col-md-2 mb-2"><label class="small fw-bold">Jml Penduduk</label><input type="number" name="jumlah_penduduk" class="form-control" value="0"></div>

                        <div class="col-md-2 mb-2"><label class="small fw-bold">Jml Laki2</label><input type="number" name="jumlah_laki" class="form-control" value="0"></div>
                        <div class="col-md-2 mb-2"><label class="small fw-bold">Jml Peremp</label><input type="number" name="jumlah_perempuan" class="form-control" value="0"></div>
                        <div class="col-md-12 mb-2">
                            <label class="small fw-bold">GeoJSON Geometry <span class="text-danger">*</span></label>
                            <textarea name="geom" class="form-control font-monospace" rows="4" required placeholder='{"type":"Polygon","coordinates":[[[...]]]}'></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary btn-sm text-white">Simpan Data</button></div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalTambahJalan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg text-start">
        <form action="{{ route('jalan-kw.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jaringan Jalan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2"><label class="small fw-bold">Nama Jalan</label><input type="text" name="nama" class="form-control"></div>
                        <div class="col-md-3 mb-2"><label class="small fw-bold">Panjang (m)</label><input type="number" step="any" name="panjang" class="form-control" value="0"></div>
                        <div class="col-md-3 mb-2"><label class="small fw-bold">Lebar (m)</label><input type="number" step="any" name="lebar" class="form-control" value="0"></div>
                        <div class="col-md-4 mb-2"><label class="small fw-bold">Kondisi</label><input type="text" name="kondisi" class="form-control"></div>
                        <div class="col-md-4 mb-2"><label class="small fw-bold">Kewenangan</label><input type="text" name="kewenangan" class="form-control"></div>
                        <div class="col-md-4 mb-2"><label class="small fw-bold">Status</label><input type="text" name="status" class="form-control"></div>
                        <div class="col-md-12 mb-2">
                            <label class="small fw-bold">GeoJSON Geometry <span class="text-danger">*</span></label>
                            <textarea name="geom" class="form-control font-monospace" rows="4" required placeholder='{"type":"LineString","coordinates":[[...]]}'></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary btn-sm text-white">Simpan Data</button></div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalImportAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin-kw.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import GeoJSON Batas Wilayah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold small mb-2">Pilih File (.geojson / .json)</label>
                        <input type="file" name="file_geojson" class="form-control border-success" accept=".geojson, .json" required>
                        <small class="text-muted mt-2 d-block">
                            *Pastikan atribut properties-nya sesuai dengan kolom: Kalurahan, Padukuhan, LUAS, JUMLAH_KK, dll.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm text-white"><i class="mdi mdi-upload"></i> Proses Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalImportJalan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('jalan-kw.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import GeoJSON Jaringan Jalan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold small mb-2">Pilih File (.geojson / .json)</label>
                        <input type="file" name="file_geojson" class="form-control border-success" accept=".geojson, .json" required>
                        <small class="text-muted mt-2 d-block">
                            *Pastikan atribut properties-nya sesuai dengan kolom: Nama, Panjang, Lebar, Kondisi, dll.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm text-white"><i class="mdi mdi-upload"></i> Proses Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalTambahGeo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('geoserver.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="mdi mdi-plus-box me-2"></i>Tambah Layer GeoServer Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">

                    <div class="card shadow-sm mb-3">
                        <div class="card-body p-3">
                            <h6 class="card-subtitle mb-3 text-muted fw-bold">1. Identitas Layer</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Workspace <span class="text-danger">*</span></label>
                                    <input type="text" name="workspace" class="form-control font-monospace" value="latihan_leaflet" required placeholder="Contoh: latihan_leaflet">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nama Layer (Store Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="layer_name" class="form-control font-monospace" required placeholder="Contoh: adminkw">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Judul Tampilan (Title Peta) <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control fw-bold" required placeholder="Contoh: Batas Wilayah Administrasi">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-3">
                        <div class="card-body p-3">
                            <h6 class="card-subtitle mb-3 text-muted fw-bold">2. Konfigurasi Server</h6>
                            <div class="row g-3 mb-2">
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Base URL GeoServer <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-server"></i></span>
                                        <input type="url" name="base_url" class="form-control font-monospace" value="http://localhost:8080/geoserver/" required>
                                    </div>
                                    <div class="form-text small">Contoh: http://localhost:8080/geoserver/ (Akhiri dengan slash '/')</div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Tipe Data</label>
                                    <select name="type" class="form-select">
                                        <option value="vector" selected>Vector (Garis/Poligon/Titik)</option>
                                        <option value="raster">Raster (Citra/Foto Udara)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Z-Index (Tumpukan)</label>
                                    <input type="number" name="z_index" class="form-control" value="10" min="0">
                                    <div class="form-text small">Default 10. Semakin besar semakin di atas.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-primary">
                        <div class="card-body p-3">
                            <h6 class="card-subtitle mb-3 text-primary fw-bold">3. Layanan & Status Peta</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light ps-0">
                                    <div>
                                        <span class="fw-bold d-block"><i class="mdi mdi-eye me-1 text-info"></i> Enable WMS</span>
                                        <small class="text-muted">Aktifkan visualisasi gambar (wajib untuk tampil).</small>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="enable_wms" value="1" checked style="transform: scale(1.3);">
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light ps-0">
                                    <div>
                                        <span class="fw-bold d-block"><i class="mdi mdi-cursor-default-click me-1 text-warning"></i> Enable WFS</span>
                                        <small class="text-muted">Aktifkan fitur klik info dan pencarian (hanya vektor).</small>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="enable_wfs" value="1" checked style="transform: scale(1.3);">
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light ps-0">
                                    <div>
                                        <span class="fw-bold d-block"><i class="mdi mdi-image-filter-hdr me-1 text-success"></i> Enable WMTS</span>
                                        <small class="text-muted">Gunakan GeoWebCache untuk loading raster cepat.</small>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="enable_wmts" value="1" style="transform: scale(1.3);">
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-success bg-opacity-10 ps-2 rounded mt-2">
                                    <div>
                                        <span class="fw-bold d-block text-success"><i class="mdi mdi-power me-1"></i> STATUS DEFAULT PETA</span>
                                        <small class="text-dark">Apakah layer langsung tampil saat peta dibuka?</small>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked style="transform: scale(1.4);">
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div> <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm text-white">Simpan Data Layer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalTambahSelf" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('cesium-self.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="mdi mdi-cube me-2"></i>Tambah Model 3D Lokal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="card shadow-sm mb-3 border-0">
                        <div class="card-body p-3">
                            <h6 class="card-subtitle mb-3 text-primary fw-bold">1. Informasi & File Model</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold">Nama Gedung / Objek <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Gedung Rektorat UGM">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Upload File Model (.glb / .gltf) <span class="text-danger">*</span></label>
                                    <input type="file" name="model_file" class="form-control border-primary" accept=".glb, .gltf" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="small fw-bold">Deskripsi Singkat</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Keterangan tambahan untuk popup peta..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3">
                            <h6 class="card-subtitle mb-3 text-primary fw-bold">2. Penempatan di Peta</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Longitude (X) <span class="text-danger">*</span></label>
                                    <input type="text" name="longitude" class="form-control" required placeholder="110.37... ">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Latitude (Y) <span class="text-danger">*</span></label>
                                    <input type="text" name="latitude" class="form-control" required placeholder="-7.76... ">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Tinggi / Elevasi (Meter)</label>
                                    <input type="number" step="any" name="height" class="form-control" value="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Rotasi / Heading (Derajat)</label>
                                    <input type="number" step="any" name="heading" class="form-control" value="0" placeholder="0 - 360">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm text-white"><i class="mdi mdi-content-save"></i> Simpan Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($selfHosteds as $self)
<div class="modal fade" id="modalEditSelf{{ $self->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('cesium-self.update', $self->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="mdi mdi-pencil-box me-2"></i>Edit Model 3D Lokal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="card shadow-sm mb-3 border-0">
                        <div class="card-body p-3">
                            <h6 class="card-subtitle mb-3 text-info fw-bold">1. Informasi & File Model</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold">Nama Gedung / Objek <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $self->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Ganti File Model <span class="text-muted fw-normal">(Opsional)</span></label>
                                    <input type="file" name="model_file" class="form-control" accept=".glb, .gltf">
                                    @if($self->model_path)
                                        <small class="text-success mt-1 d-block"><i class="mdi mdi-check"></i> File aktif: {{ basename($self->model_path) }}</small>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <label class="small fw-bold">Deskripsi Singkat</label>
                                    <textarea name="description" class="form-control" rows="2">{{ $self->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3">
                            <h6 class="card-subtitle mb-3 text-info fw-bold">2. Penempatan di Peta</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Longitude (X) <span class="text-danger">*</span></label>
                                    <input type="text" name="longitude" class="form-control" value="{{ $self->longitude }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Latitude (Y) <span class="text-danger">*</span></label>
                                    <input type="text" name="latitude" class="form-control" value="{{ $self->latitude }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Tinggi / Elevasi (Meter)</label>
                                    <input type="number" step="any" name="height" class="form-control" value="{{ $self->height }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Rotasi / Heading (Derajat)</label>
                                    <input type="number" step="any" name="heading" class="form-control" value="{{ $self->heading }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info btn-sm text-white"><i class="mdi mdi-content-save"></i> Update Data</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade" id="modalTambahIon" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('cesium-ion.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="mdi mdi-cloud-upload me-2"></i>Tambah Asset Cesium Ion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="small fw-bold">Nama Lokasi / Aset <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="Contoh: Fotogrametri Kampus UGM">
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">Ion Asset ID (Angka) <span class="text-danger">*</span></label>
                                <input type="number" name="ion_asset_id" class="form-control font-monospace border-dark" required placeholder="Contoh: 1234567">
                                <small class="text-muted d-block mt-1">Dapatkan ID ini dari dashboard Cesium Ion Anda.</small>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Informasi singkat tentang lokasi ini..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="submit" class="btn btn-dark btn-sm text-white"><i class="mdi mdi-content-save"></i> Simpan Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($ions as $ion)
<div class="modal fade" id="modalEditIon{{ $ion->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('cesium-ion.update', $ion->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="mdi mdi-pencil-box me-2"></i>Edit Asset Cesium Ion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                     <div class="card shadow-sm border-0">
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="small fw-bold">Nama Lokasi / Aset <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $ion->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">Ion Asset ID (Angka) <span class="text-danger">*</span></label>
                                <input type="number" name="ion_asset_id" class="form-control font-monospace border-secondary" value="{{ $ion->ion_asset_id }}" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3">{{ $ion->description }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="submit" class="btn btn-secondary btn-sm text-white"><i class="mdi mdi-content-save"></i> Update Data</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endsection
