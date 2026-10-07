@extends('layouts.app')
@section('title', 'Laporan & Filter Aset')

@section('content')
<!-- Tab Navigasi Pelaporan -->
<div class="no-print" style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; overflow-x: auto;">
    <a href="{{ route('laporan.index') }}" class="btn {{ request()->routeIs('laporan.index') ? 'btn-primary' : 'btn-outline' }}" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-weight: 500; {{ request()->routeIs('laporan.index') ? '' : 'border: none; color: var(--text-muted);' }}">
        <i class="fa-solid fa-layer-group"></i> Laporan Aset Aktif
    </a>
    <a href="{{ route('laporan.keluar') }}" class="btn {{ request()->routeIs('laporan.keluar') ? 'btn-primary' : 'btn-outline' }}" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-weight: 500; {{ request()->routeIs('laporan.keluar') ? '' : 'border: none; color: var(--text-muted);' }}">
        <i class="fa-solid fa-right-from-bracket"></i> Laporan Barang Keluar
    </a>
</div>

<div class="card no-print">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-filter"></i> Smart Filter Bar
        </div>
    </div>
    <form action="{{ route('laporan.index') }}" method="GET">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Ruangan (KIR)</label>
                <select name="ruangan_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Ruangan</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori / Jenis</label>
                <select name="kategori_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kondisi</label>
                <select name="kondisi" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>
        </div>
        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
            <div class="form-group">
                <label class="form-label">Tahun Perolehan</label>
                <select name="tahun_perolehan" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    @foreach($tahuns as $t)
                        <option value="{{ $t }}" {{ request('tahun_perolehan') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status Ketersediaan</label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Sumber Dana</label>
                <select name="sumber_dana_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Sumber Dana</option>
                    @foreach($sumberDanas as $s)
                        <option value="{{ $s->id }}" {{ request('sumber_dana_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="display: flex; align-items: flex-end; gap: 0.5rem;">
                <a href="{{ route('laporan.index') }}" class="btn btn-outline" style="flex: 1; justify-content: center;">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
                <a href="{{ route('laporan.export', request()->query()) }}" class="btn btn-outline" style="flex: 1; justify-content: center; color: #10b981; border-color: #10b981;">
                    <i class="fa-solid fa-file-excel"></i> Export
                </a>
            </div>
        </div>
    </form>
</div>

<div class="stats-grid no-print">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-cubes"></i></div>
        <div class="stat-content">
            <h3>{{ $stats['total'] }}</h3>
            <p>Total Hasil Filter</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-check"></i></div>
        <div class="stat-content">
            <h3>{{ $stats['baik'] }}</h3>
            <p>Kondisi Baik</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="stat-content">
            <h3>{{ $stats['rusak_ringan'] }}</h3>
            <p>Rusak Ringan</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-circle-xmark"></i></div>
        <div class="stat-content">
            <h3>{{ $stats['rusak_berat'] }}</h3>
            <p>Rusak Berat</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-print no-print"></i> 
            Data Laporan Aset 
            @if(request('ruangan_id'))
                - Kartu Inventaris Ruangan (KIR)
            @endif
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-primary btn-print">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>
    
    <!-- Bagian ini akan muncul hanya saat print (Header Kop Surat) -->
    <div style="display: none; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; align-items: center;" class="print-only flex-container">
        <div style="width: 100px; text-align: center;">
            @if(isset($profil_sekolah) && $profil_sekolah->logo)
                <img src="{{ asset('uploads/' . $profil_sekolah->logo) }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
            @else
                <div style="width: 80px; height: 80px; border: 1px solid #000; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 10px;">LOGO</div>
            @endif
        </div>
        <div style="flex: 1; text-align: center;">
            <h2 style="margin: 0; font-size: 1.5rem; text-transform: uppercase; font-weight: bold;">{{ $profil_sekolah->nama_sekolah ?? 'NAMA SEKOLAH' }}</h2>
            <p style="margin: 0; font-size: 1rem; font-weight: bold;">{{ $profil_sekolah->sub_nama ?? 'Sub Nama Sekolah' }}</p>
            <p style="margin: 0; font-size: 0.85rem;">{{ $profil_sekolah->alamat ?? 'Alamat Lengkap Sekolah' }}</p>
            <p style="margin: 0; font-size: 0.85rem;">Telp: {{ $profil_sekolah->telepon ?? '-' }} | Email: {{ $profil_sekolah->email ?? '-' }}</p>
        </div>
        <div style="width: 100px;"></div> <!-- Spacer -->
    </div>

    <div style="display: none; text-align: center; margin-bottom: 1.5rem;" class="print-only">
        <h3 style="margin: 0; text-transform: uppercase; text-decoration: underline; font-size: 1.1rem; font-weight: bold;">
            @if(request('ruangan_id')) KARTU INVENTARIS RUANGAN (KIR) @else LAPORAN DATA ASET @endif
        </h3>
        
        <!-- Filter Info -->
        <div style="margin-top: 10px; font-size: 0.9rem; text-align: center; display: flex; justify-content: center; gap: 20px;">
            @if(request('ruangan_id'))
                <span><strong>Ruangan:</strong> {{ \App\Models\Ruangan::find(request('ruangan_id'))->nama ?? '-' }}</span>
            @endif
            @if(request('kategori_id'))
                <span><strong>Kategori:</strong> {{ \App\Models\Kategori::find(request('kategori_id'))->nama ?? '-' }}</span>
            @endif
        </div>
    </div>

    <div class="table-responsive" style="border: none;">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Unit</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th>Kategori</th>
                    <th>Tahun</th>
                    <th>Tgl Masuk</th>
                    <th>Harga (Rp)</th>
                    <th>Ruangan</th>
                    <th>Kondisi</th>
                    <th>Sumber Dana</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $index => $unit)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $unit->kode_unit }}</strong></td>
                    <td>{{ $unit->barang->nama }}</td>
                    <td>{{ $unit->barang->merk ?? '-' }}</td>
                    <td>{{ $unit->barang->kategori->nama }}</td>
                    <td>{{ $unit->barang->tahun_perolehan ?? '-' }}</td>
                    <td>{{ $unit->barang->tanggal_masuk ? \Carbon\Carbon::parse($unit->barang->tanggal_masuk)->format('d M Y') : '-' }}</td>
                    <td>{{ $unit->barang->harga_perolehan ? number_format($unit->barang->harga_perolehan, 0, ',', '.') : '-' }}</td>
                    <td>{{ $unit->ruangan->nama }}</td>
                    <td>
                        @if($unit->kondisi == 'Baik')
                            <span class="badge badge-success">{{ $unit->kondisi }}</span>
                        @elseif($unit->kondisi == 'Rusak Ringan')
                            <span class="badge badge-warning">{{ $unit->kondisi }}</span>
                        @else
                            <span class="badge badge-danger">{{ $unit->kondisi }}</span>
                        @endif
                    </td>
                    <td>{{ $unit->sumberDana->nama ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 2rem;">
                        Tidak ada data yang sesuai dengan filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($units->hasPages())
    <div class="no-print" style="padding: 1rem; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
        {{ $units->links() }}
    </div>
    @endif
    
    <!-- Footer Print -->
    <div style="display: none; margin-top: 3rem; justify-content: flex-end;" class="print-only">
        <div style="text-align: center; width: 200px;">
            <p>Metro, {{ date('d F Y') }}</p>
            <p>Mengetahui,</p>
            <br><br><br>
            <p>_____________________</p>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        margin: 1.5cm;
    }
    .print-only {
        display: block !important;
    }
    .print-only.flex-container {
        display: flex !important;
    }
    .table-responsive {
        overflow: visible !important;
        border: none !important;
    }
    table.table {
        border-collapse: collapse !important;
        width: 100% !important;
        font-size: 10pt !important;
        margin-bottom: 0 !important;
    }
    table.table th, table.table td {
        border: 1px solid black !important;
        padding: 4px 6px !important; /* Rapat/Compact */
        color: black !important;
    }
    table.table th {
        background-color: #f0f0f0 !important;
        -webkit-print-color-adjust: exact;
        font-weight: bold !important;
        text-align: center !important;
    }
    .badge {
        border: none !important;
        padding: 0 !important;
        background: transparent !important;
        color: black !important;
        font-weight: normal !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .card-header {
        display: none !important;
    }
}
</style>
@endsection
