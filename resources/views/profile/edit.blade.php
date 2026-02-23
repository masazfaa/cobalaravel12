@extends('layouts.admin')

@section('title', 'Profile Settings - WebGIS Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12 col-xl-10">

        <div class="row">
            <div class="col-md-7 d-flex flex-column gap-3 mb-4 mb-md-0">

                <div class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title text-primary mb-2"><i class="mdi mdi-account-edit me-2"></i>Informasi Profile</h5>
                        <p class="card-description text-muted small mb-3">Perbarui informasi profil dan alamat email akun Anda.</p>

                        <form method="post" action="{{ route('profile.update') }}" class="forms-sample">
                            @csrf
                            @method('patch')

                            <div class="form-group mb-3">
                                <label for="name" class="fw-bold small">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="fw-bold small">Alamat Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                                @error('email')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center gap-3 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm text-white mb-0">Simpan Perubahan</button>

                                @if (session('status') === 'profile-updated')
                                    <p class="text-success mb-0 fw-semibold small"><i class="mdi mdi-check-circle me-1"></i>Tersimpan.</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title text-primary mb-2"><i class="mdi mdi-lock-reset me-2"></i>Ubah Password</h5>
                        <p class="card-description text-muted small mb-3">Gunakan password yang panjang dan acak agar tetap aman.</p>

                        <form method="post" action="{{ route('password.update') }}" class="forms-sample">
                            @csrf
                            @method('put')

                            <div class="form-group mb-3">
                                <label for="update_password_current_password" class="fw-bold small">Password Saat Ini</label>
                                <input type="password" class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" id="update_password_current_password" name="current_password" autocomplete="current-password">
                                @if($errors->updatePassword->has('current_password'))
                                    <span class="text-danger small">{{ $errors->updatePassword->first('current_password') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <label for="update_password_password" class="fw-bold small">Password Baru</label>
                                <input type="password" class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" id="update_password_password" name="password" autocomplete="new-password">
                                @if($errors->updatePassword->has('password'))
                                    <span class="text-danger small">{{ $errors->updatePassword->first('password') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <label for="update_password_password_confirmation" class="fw-bold small">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
                                @if($errors->updatePassword->has('password_confirmation'))
                                    <span class="text-danger small">{{ $errors->updatePassword->first('password_confirmation') }}</span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-3 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm text-white mb-0">Update Password</button>

                                @if (session('status') === 'password-updated')
                                    <p class="text-success mb-0 fw-semibold small"><i class="mdi mdi-check-circle me-1"></i>Berhasil diperbarui.</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="col-md-5">
                <div class="card shadow-sm border-danger h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title text-danger mb-2"><i class="mdi mdi-alert-circle me-2"></i>Hapus Akun</h5>
                        <p class="card-description text-muted small mb-3">Setelah akun Anda dihapus, semua data terkait akan hilang permanen. Harap unduh data sebelum menghapus.</p>

                        <form method="post" action="{{ route('profile.destroy') }}" class="forms-sample" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus akun ini secara permanen?');">
                            @csrf
                            @method('delete')

                            <div class="form-group mb-3 mt-4">
                                <label for="password" class="fw-bold text-danger small">Password Konfirmasi</label>
                                <input type="password" class="form-control border-danger @error('password', 'userDeletion') is-invalid @enderror" id="password" name="password" placeholder="Masukkan password" required>
                                @error('password', 'userDeletion')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-danger btn-sm text-white mt-2 w-100">
                                <i class="mdi mdi-delete-forever me-1"></i> Hapus Akun Saya
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
