@extends('layouts.app')
@section('title', 'Transaksi Barang Keluar')

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
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div class="card-title">
            <i class="fa-solid fa-right-from-bracket"></i> Proses Barang Keluar
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <button type="button" class="btn btn-success" onclick="startScannerForKeluar()">
                <i class="fa-solid fa-qrcode"></i> Scan Barang
            </button>
            <button type="button" class="btn btn-danger" onclick="openKeluarModal()">
                <i class="fa-solid fa-right-from-bracket"></i> Proses Barang Keluar
            </button>
            <input type="text" class="form-control table-search" placeholder="Cari kode, barang, ruangan..." style="width: 250px;">
        </div>
    </div>
    
    <div class="table-responsive" style="margin-bottom: 1.5rem;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>Kode Unit</th>
                    <th>Nama Barang</th>
                    <th>Ruangan Saat Ini</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $unit)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $unit->id }}" class="unit-checkbox">
                    </td>
                    <td><strong>{{ $unit->kode_unit }}</strong></td>
                    <td>{{ $unit->barang->nama }}</td>
                    <td><span class="badge badge-info">{{ $unit->ruangan->nama ?? '-' }}</span></td>
                    <td>
                        @if($unit->kondisi == 'Baik')
                            <span class="badge badge-success">{{ $unit->kondisi }}</span>
                        @elseif($unit->kondisi == 'Rusak Ringan')
                            <span class="badge badge-warning">{{ $unit->kondisi }}</span>
                        @else
                            <span class="badge badge-danger">{{ $unit->kondisi }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted);">
                        Tidak ada barang yang tersedia untuk dikeluarkan.
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

<!-- Modal Barang Keluar -->
<div id="keluarModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; border-radius: 8px; width: 90%; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <form id="keluarForm" action="{{ route('barang-keluar.store') }}" method="POST">
            @csrf
            <div id="hiddenInputsContainer"></div>
            
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.1rem; color: var(--danger);"><i class="fa-solid fa-box-open"></i> Form Barang Keluar</h3>
                <span onclick="closeKeluarModal()" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            </div>
            
            <div id="keluarModalInfo" style="padding: 1rem 1.5rem 0 1.5rem;"></div>
            
            <div style="padding: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Tanggal Keluar</label>
                    <input type="date" name="tanggal_keluar" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Pengeluaran</label>
                    <select name="jenis" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Dimusnahkan">Dimusnahkan (Rusak Berat)</option>
                        <option value="Dijual">Dijual</option>
                        <option value="Dihibahkan">Dihibahkan</option>
                        <option value="Hilang">Hilang</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Alasan dikeluarkan, Berita Acara, dsb." required></textarea>
                </div>
                <div class="alert alert-warning" style="margin-bottom: 0;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <strong>Perhatian:</strong> Barang yang sudah dikeluarkan akan berstatus 'Dihapus' dan tidak akan muncul di laporan aset aktif.
                </div>
            </div>
            
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 0.5rem; background: var(--bg-color);">
                <button type="button" class="btn btn-outline" onclick="closeKeluarModal()">Batal</button>
                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-check"></i> Konfirmasi Barang Keluar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.unit-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            if(this.checked) {
                cb.closest('tr').style.backgroundColor = 'rgba(241, 85, 108, 0.05)';
            } else {
                cb.closest('tr').style.backgroundColor = '';
            }
        });
    });

    document.querySelectorAll('.unit-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            if(this.checked) {
                this.closest('tr').style.backgroundColor = 'rgba(241, 85, 108, 0.05)';
            } else {
                this.closest('tr').style.backgroundColor = '';
            }
        });
    });

    function openKeluarModal() {
        let selected = document.querySelectorAll('.unit-checkbox:checked');
        if (selected.length === 0) {
            alert('Pilih minimal 1 unit barang untuk dikeluarkan!');
            return;
        }
        
        let container = document.getElementById('hiddenInputsContainer');
        container.innerHTML = '';
        
        selected.forEach(cb => {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'unit_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });

        document.getElementById('keluarModalInfo').innerHTML = `
            <div style="background: #fee2e2; padding: 1rem; border-radius: 8px; border: 1px solid #fecaca;">
                <strong><i class="fa-solid fa-circle-info"></i> ${selected.length} Aset Terpilih</strong> untuk dikeluarkan.
            </div>
        `;

        document.getElementById('keluarModal').style.display = 'block';
    }

    function closeKeluarModal() {
        document.getElementById('keluarModal').style.display = 'none';
    }

    window.onclick = function(event) {
        let modal = document.getElementById('keluarModal');
        if (event.target == modal) {
            closeKeluarModal();
        }
    }

    function startScannerForKeluar() {
        openScanner(function(decodedText) {
            fetch(`/api/scan-unit/${encodeURIComponent(decodedText)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let container = document.getElementById('hiddenInputsContainer');
                        container.innerHTML = '';
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'unit_ids[]';
                        input.value = data.data.id;
                        container.appendChild(input);
                        
                        let currentRoom = data.data.ruangan ? data.data.ruangan.nama : '-';
                        document.getElementById('keluarModalInfo').innerHTML = `
                            <div style="background: #fee2e2; padding: 1rem; border-radius: 8px; border: 1px solid #fecaca;">
                                <div style="font-weight: bold; margin-bottom: 0.5rem;"><i class="fa-solid fa-qrcode"></i> Aset Terpilih (Hasil Scan):</div>
                                <div><strong>Kode:</strong> ${data.data.kode_unit}</div>
                                <div><strong>Barang:</strong> ${data.data.barang.nama}</div>
                                <div><strong>Ruangan:</strong> ${currentRoom}</div>
                            </div>
                        `;
                        
                        document.getElementById('keluarModal').style.display = 'block';
                        toastr.success('Barang ditemukan. Silakan lengkapi form pengeluaran.');
                    } else {
                        toastr.error(data.message);
                    }
                })
                .catch(error => toastr.error('Terjadi kesalahan sistem saat mencari aset.'));
        });
    }
</script>
@include('components.scanner-modal')
@endpush
