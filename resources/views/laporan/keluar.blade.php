@extends('layouts.app')
@section('title', 'Laporan Barang Keluar')

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
            <i class="fa-solid fa-filter"></i> Filter Barang Keluar
        </div>
    </div>
    <form action="{{ route('laporan.keluar') }}" method="GET">
        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
            <div class="form-group">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}" onchange="this.form.submit()">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}" onchange="this.form.submit()">
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Pengeluaran</label>
                <select name="jenis" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    <option value="Dimusnahkan" {{ request('jenis') == 'Dimusnahkan' ? 'selected' : '' }}>Dimusnahkan</option>
                    <option value="Dijual" {{ request('jenis') == 'Dijual' ? 'selected' : '' }}>Dijual</option>
                    <option value="Dihibahkan" {{ request('jenis') == 'Dihibahkan' ? 'selected' : '' }}>Dihibahkan</option>
                    <option value="Hilang" {{ request('jenis') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                    <option value="Lainnya" {{ request('jenis') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="form-group" style="display: flex; align-items: flex-end; gap: 0.5rem;">
                <a href="{{ route('laporan.keluar') }}" class="btn btn-outline" style="flex: 1; justify-content: center;">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
                <a href="{{ route('laporan.export-keluar', request()->query()) }}" class="btn btn-outline" style="flex: 1; justify-content: center; color: #10b981; border-color: #10b981;">
                    <i class="fa-solid fa-file-excel"></i> Export
                </a>
            </div>
        </div>
    </form>
</div>

<div class="stats-grid no-print">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-right-from-bracket"></i></div>
        <div class="stat-content">
            <h3>{{ $stats['total'] }}</h3>
            <p>Total Dikeluarkan</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-fire"></i></div>
        <div class="stat-content">
            <h3>{{ $stats['dimusnahkan'] }}</h3>
            <p>Dimusnahkan</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div class="stat-content">
            <h3>{{ $stats['dijual'] }}</h3>
            <p>Dijual</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fa-solid fa-question"></i></div>
        <div class="stat-content">
            <h3>{{ $stats['hilang'] }}</h3>
            <p>Hilang</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-print no-print"></i> Data Riwayat Barang Keluar
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
            LAPORAN BARANG KELUAR / DIHAPUS
        </h3>
    </div>

    <div class="table-responsive" style="border: none;">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Keluar</th>
                    <th>Kode Unit</th>
                    <th>Nama Barang</th>
                    <th>Tahun</th>
                    <th>Harga (Rp)</th>
                    <th>Jenis Pengeluaran</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($keluars as $index => $keluar)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d M Y') }}</td>
                    <td><strong>{{ $keluar->unitBarang->kode_unit ?? '-' }}</strong></td>
                    <td>{{ $keluar->unitBarang->barang->nama ?? '-' }}</td>
                    <td>{{ $keluar->unitBarang->barang->tahun_perolehan ?? '-' }}</td>
                    <td>{{ isset($keluar->unitBarang->barang->harga_perolehan) ? number_format($keluar->unitBarang->barang->harga_perolehan, 0, ',', '.') : '-' }}</td>
                    <td><span class="badge badge-danger">{{ $keluar->jenis }}</span></td>
                    <td>{{ $keluar->keterangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem;">
                        Tidak ada data riwayat barang keluar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($keluars->hasPages())
    <div class="no-print" style="padding: 1rem; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
        {{ $keluars->links() }}
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
