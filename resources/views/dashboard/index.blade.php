@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div class="stat-content">
            <h3>{{ $totalAset }}</h3>
            <p>Total Aset Aktif</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-check-circle"></i></div>
        <div class="stat-content">
            <h3>{{ $asetBaik }}</h3>
            <p>Kondisi Baik</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="stat-content">
            <h3>{{ $asetRusak }}</h3>
            <p>Rusak (Ringan & Berat)</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fa-solid fa-hand-holding-hand"></i></div>
        <div class="stat-content">
            <h3>{{ $peminjamanAktif }}</h3>
            <p>Peminjaman Aktif</p>
        </div>
    </div>
</div>

<div class="form-row" style="grid-template-columns: 1fr 1fr;">
    <!-- Chart / Info Kategori -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
            <div class="card-title"><i class="fa-solid fa-chart-pie"></i> Kondisi Aset</div>
        </div>
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; min-height: 250px;">
            <div style="position: relative; height: 250px; width: 100%; display: flex; justify-content: center;">
                <canvas id="kondisiChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Peminjaman Terbaru -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
            <div class="card-title"><i class="fa-solid fa-hand-holding-hand"></i> Peminjaman Terbaru</div>
        </div>
        <div class="table-responsive">
            <table class="table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Barang</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPeminjaman as $pem)
                    <tr>
                        <td>{{ $pem->peminjam->nama ?? $pem->nama_peminjam ?? '-' }}</td>
                        <td>{{ $pem->unitBarang->barang->nama ?? '-' }} ({{ $pem->unitBarang->kode_unit ?? '-' }})</td>
                        <td>{{ \Carbon\Carbon::parse($pem->tanggal_pinjam)->format('d M Y') }}</td>
                        <td>
                            @if($pem->status == 'Dipinjam')
                                <span class="badge badge-warning">Dipinjam</span>
                            @else
                                <span class="badge badge-success">Dikembalikan</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data peminjaman</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="form-row" style="grid-template-columns: 1fr 1fr;">
    <!-- Mutasi Terbaru -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
            <div class="card-title"><i class="fa-solid fa-right-left"></i> Mutasi Terbaru</div>
        </div>
        <div class="table-responsive">
            <table class="table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Dari</th>
                        <th>Ke</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentMutasi as $mut)
                    <tr>
                        <td>{{ $mut->unitBarang->barang->nama ?? '-' }}</td>
                        <td>{{ $mut->ruanganAsal->nama ?? '-' }}</td>
                        <td>{{ $mut->ruanganTujuan->nama ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($mut->created_at)->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada mutasi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Barang Keluar Terbaru -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
            <div class="card-title"><i class="fa-solid fa-right-from-bracket"></i> Barang Keluar / Dihapus Terbaru</div>
        </div>
        <div class="table-responsive">
            <table class="table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentKeluar as $kel)
                    <tr>
                        <td>{{ $kel->unitBarang->barang->nama ?? '-' }} ({{ $kel->unitBarang->kode_unit ?? '-' }})</td>
                        <td><span class="badge badge-danger">{{ $kel->jenis }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($kel->tanggal_keluar)->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Belum ada barang keluar</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('kondisiChart').getContext('2d');
        const kondisiChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
                datasets: [{
                    data: [{{ $kondisiData['Baik'] }}, {{ $kondisiData['Rusak Ringan'] }}, {{ $kondisiData['Rusak Berat'] }}],
                    backgroundColor: [
                        '#1abc9c', // success
                        '#f7b84b', // warning
                        '#f1556c'  // danger
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 13
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush
@endsection
