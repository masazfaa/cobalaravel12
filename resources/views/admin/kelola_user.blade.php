@extends('layouts.admin')

@section('title', 'Kelola User Pending - WebGIS Admin')

@section('content')
<div class="row">
    <div class="col-sm-12">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title">Daftar Permintaan Pendaftaran</h4>
                        <p class="card-description">Setujui untuk mengaktifkan, atau Hapus untuk menolak.</p>
                    </div>
                    <div>
                        <span class="badge bg-primary fs-6">
                            Total Pending: {{ $users->count() }}
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Tanggal Daftar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">

                                            <form action="{{ route('user.approve', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengaktifkan user {{ $user->name }}?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm text-white d-flex align-items-center">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i> Setujui
                                                </button>
                                            </form>

                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENOLAK dan MENGHAPUS data {{ $user->name }}? Tindakan ini tidak bisa dibatalkan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm text-white d-flex align-items-center">
                                                    <i class="mdi mdi-delete-outline me-1"></i> Hapus
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="mdi mdi-account-search mdi-48px"></i>
                                        <p class="mt-2 mb-0 fw-bold">Tidak ada permintaan pendaftaran baru.</p>
                                        <small>Semua user sudah diproses.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
