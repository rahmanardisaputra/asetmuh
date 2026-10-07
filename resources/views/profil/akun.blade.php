@extends('layouts.app')
@section('title', 'Profil Akun')

@section('content')
<div class="row" style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
    <div class="col-md-4" style="flex: 0 0 auto; width: 33.33333333%;">
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 2rem;">
                <div style="width: 120px; height: 120px; border-radius: 50%; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 1.5rem auto;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h4 style="margin-bottom: 0.5rem; color: var(--text-main);">Admin Sekolah</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem;">admin@asetmuh1metro.com</p>
                <div style="margin-top: 1.5rem;">
                    <span class="badge badge-primary">Administrator</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8" style="flex: 1 1 0; min-width: 0;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-user-pen"></i> Edit Profil Akun
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('profil.akun.update') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" value="Admin Sekolah" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="admin@asetmuh1metro.com" required>
                    </div>
                    
                    <hr style="margin: 2rem 0; border: 0; border-top: 1px solid var(--border-color);">
                    
                    <h5 style="margin-bottom: 1rem; color: var(--text-main);">Ubah Password (Kosongkan jika tidak ingin mengubah)</h5>
                    
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan password baru">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Ulangi password baru">
                    </div>
                    
                    <div style="margin-top: 2rem; text-align: right;">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
