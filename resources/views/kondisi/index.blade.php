@extends('layouts.app')
@section('title', 'Pembaruan Kondisi Barang')

@section('content')
<!-- Tab Navigasi Transaksi -->
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; overflow-x: auto;">
    <a href="{{ route('mutasi.index') }}" class="btn {{ request()->routeIs('mutasi.*') ? 'btn-primary' : 'btn-outline' }}" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-weight: 500; {{ request()->routeIs('mutasi.*') ? '' : 'border: none; color: var(--text-muted);' }}">
        <i class="fa-solid fa-arrow-right-arrow-left"></i> Mutasi Ruangan
    </a>
    <a href="{{ route('kondisi.index') }}" class="btn {{ request()->routeIs('kondisi.*') ? 'btn-primary' : 'btn-outline' }}" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-weight: 500; {{ request()->routeIs('kondisi.*') ? '' : 'border: none; color: var(--text-muted);' }}">
        <i class="fa-solid fa-screwdriver-wrench"></i> Pembaruan Kondisi
    </a>
    <a href="{{ route('peminjaman.index') }}" class="btn {{ request()->routeIs('peminjaman.*') ? 'btn-primary' : 'btn-outline' }}" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-weight: 500; {{ request()->routeIs('peminjaman.*') ? '' : 'border: none; color: var(--text-muted);' }}">
        <i class="fa-solid fa-hand-holding-hand"></i> Peminjaman Barang
    </a>
    <a href="{{ route('barang-keluar.index') }}" class="btn {{ request()->routeIs('barang-keluar.*') ? 'btn-primary' : 'btn-outline' }}" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-weight: 500; {{ request()->routeIs('barang-keluar.*') ? '' : 'border: none; color: var(--text-muted);' }}">
        <i class="fa-solid fa-box-open"></i> Barang Keluar
    </a>
</div>
<div class="card">
    <div class="card-header" style="border-bottom: none; padding-bottom: 0;">
        <div class="card-title">
            <i class="fa-solid fa-screwdriver-wrench"></i> Update Kondisi
        </div>
    </div>
    
    <!-- Filter Form -->
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #f8fafc;">
        <form action="{{ route('kondisi.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <button type="submit" style="display: none;"></button>
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem;">Pencarian (Kode / Nama)</label>
                <input type="text" name="search" class="form-control" placeholder="Cari kode unit, nama barang..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem;">Ruangan</label>
                <select name="ruangan_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Ruangan</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem;">Kategori</label>
                <select name="kategori_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem;">Status Kondisi</label>
                <select name="kondisi" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: flex-end; padding-bottom: 0.15rem;">
                <a href="{{ route('kondisi.index') }}" class="btn btn-outline" style="padding: 0.45rem 1rem;">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Unit</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Ruangan</th>
                    <th>Kondisi Saat Ini</th>
                    <th>Ubah Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $unit)
                <tr>
                    <td><strong>{{ $unit->kode_unit }}</strong></td>
                    <td>{{ $unit->barang->nama }}</td>
                    <td><span class="badge badge-primary">{{ $unit->barang->kategori->nama ?? '-' }}</span></td>
                    <td>{{ $unit->ruangan->nama ?? '-' }}</td>
                    <td>
                        @if($unit->kondisi == 'Baik')
                            <span class="badge badge-success">{{ $unit->kondisi }}</span>
                        @elseif($unit->kondisi == 'Rusak Ringan')
                            <span class="badge badge-warning">{{ $unit->kondisi }}</span>
                        @else
                            <span class="badge badge-danger">{{ $unit->kondisi }}</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('kondisi.update') }}" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                            @csrf
                            <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                            <select name="kondisi" class="form-control" style="padding: 0.25rem 0.5rem; width: auto;" onchange="this.form.submit()">
                                <option value="Baik" {{ $unit->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak Ringan" {{ $unit->kondisi == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="Rusak Berat" {{ $unit->kondisi == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted);">
                        Belum ada unit barang atau tidak ada yang sesuai kriteria filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($units->hasPages())
    <div style="padding: 1rem; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
        {{ $units->links() }}
    </div>
    @endif
</div>
@endsection
