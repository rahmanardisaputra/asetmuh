@extends('layouts.app')
@section('title', 'Profil Sekolah')

@section('content')
<div class="row" style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
    <div class="col-md-4" style="flex: 0 0 auto; width: 33.33333333%;">
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 2rem;">
                <div style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; background-color: var(--bg-main); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; border: 2px dashed var(--border-color);">
                    @if($profil_sekolah && $profil_sekolah->logo)
                        <img src="{{ asset('uploads/' . $profil_sekolah->logo) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-school" style="font-size: 3rem; color: var(--text-muted);"></i>
                    @endif
                </div>
                <h4 style="margin-bottom: 0.5rem; color: var(--text-main);">{{ $profil_sekolah->nama_sekolah ?? 'Nama Sekolah' }}</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem;">{{ $profil_sekolah->sub_nama ?? 'Sub Nama / Yayasan' }}</p>
                <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem; justify-content: center;">
                    <span class="badge badge-success">Aktif</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8" style="flex: 1 1 0; min-width: 0;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-school"></i> Informasi Profil Sekolah
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('profil.sekolah.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Upload Logo Sekolah</label>
                        <input type="file" class="form-control" name="logo" accept="image/*">
                        <small style="color: var(--text-muted);">Biarkan kosong jika tidak ingin mengubah logo. (Maksimal 2MB, format: JPG, PNG)</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nama Sekolah (Baris Pertama)</label>
                            <input type="text" class="form-control" name="nama_sekolah" value="{{ $profil_sekolah->nama_sekolah ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sub Nama / Keterangan (Baris Kedua)</label>
                            <input type="text" class="form-control" name="sub_nama" value="{{ $profil_sekolah->sub_nama ?? '' }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" name="alamat" rows="3">{{ $profil_sekolah->alamat ?? '' }}</textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email Sekolah</label>
                            <input type="email" class="form-control" name="email" value="{{ $profil_sekolah->email ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" name="telepon" value="{{ $profil_sekolah->telepon ?? '' }}">
                        </div>
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
