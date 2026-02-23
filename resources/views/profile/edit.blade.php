@extends('layouts.admin')

@section('title', 'Profile Settings - WebGIS Admin')

@section('content')
<div class="row">

    <div class="col-md-6 grid-margin stretch-card d-flex flex-column gap-4">

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title text-primary"><i class="mdi mdi-account-edit me-2"></i>Informasi Profile</h4>
                <p class="card-description">Perbarui informasi profil dan alamat email akun Anda.</p>

                <form method="post" action="{{ route('profile.update') }}" class="forms-sample">
                    @csrf
                    @method('patch')

                    <div class="form-group">
                        <label for="name" class="fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="fw-bold">Alamat Email</label>
                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary text-white mb-0">Simpan Perubahan</button>

                        @if (session('status') === 'profile-updated')
                            <p class="text-success mb-0 fw-semibold"><i class="mdi mdi-check-circle me-1"></i>Tersimpan.</p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h4 class="card-title text-primary"><i class="mdi mdi-lock-reset me-2"></i>Ubah Password</h4>
                <p class="card-description">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>

                <form method="post" action="{{ route('password.update') }}" class="forms-sample">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label for="update_password_current_password" class="fw-bold">Password Saat Ini</label>
                        <input type="password" class="form-control form-control-lg @if($errors->updatePassword->has('current_password')) is-invalid @endif" id="update_password_current_password" name="current_password" autocomplete="current-password">
                        @if($errors->updatePassword->has('current_password'))
                            <span class="text-danger small">{{ $errors->updatePassword->first('current_password') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="update_password_password" class="fw-bold">Password Baru</label>
                        <input type="password" class="form-control form-control-lg @if($errors->updatePassword->has('password')) is-invalid @endif" id="update_password_password" name="password" autocomplete="new-password">
                        @if($errors->updatePassword->has('password'))
                            <span class="text-danger small">{{ $errors->updatePassword->first('password') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="update_password_password_confirmation" class="fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control form-control-lg @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
                        @if($errors->updatePassword->has('password_confirmation'))
                            <span class="text-danger small">{{ $errors->updatePassword->first('password_confirmation') }}</span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary text-white mb-0">Update Password</button>

                        @if (session('status') === 'password-updated')
                            <p class="text-success mb-0 fw-semibold"><i class="mdi mdi-check-circle me-1"></i>Berhasil diperbarui.</p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="col-md-6 grid-margin stretch-card">
        <div class="card shadow-sm border-danger">
            <div class="card-body">
                <h4 class="card-title text-danger"><i class="mdi mdi-alert-circle me-2"></i>Hapus Akun</h4>
                <p class="card-description">Setelah akun Anda dihapus, semua sumber daya dan data yang terkait akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.</p>

                <form method="post" action="{{ route('profile.destroy') }}" class="forms-sample" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus akun ini secara permanen?');">
                    @csrf
                    @method('delete')

                    <div class="form-group mt-4">
                        <label for="password" class="fw-bold text-danger">Masukkan Password Anda untuk Konfirmasi Penghapusan</label>
                        <input type="password" class="form-control form-control-lg border-danger @error('password', 'userDeletion') is-invalid @enderror" id="password" name="password" placeholder="Password Anda" required>
                        @error('password', 'userDeletion')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger text-white mt-3">
                        <i class="mdi mdi-delete-forever me-1"></i> Hapus Akun Saya
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
