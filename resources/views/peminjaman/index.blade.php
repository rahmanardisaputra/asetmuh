@extends('layouts.app')
@section('title', 'Peminjaman & Pengembalian Barang')

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
            <i class="fa-solid fa-list-check"></i> Riwayat Peminjaman Aktif
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" class="btn btn-success" onclick="startScannerForPeminjaman()">
                <i class="fa-solid fa-qrcode"></i> Scan Barang
            </button>
            <button type="button" class="btn btn-primary" onclick="openPinjamModal()">
                <i class="fa-solid fa-plus"></i> Pinjam Barang Baru
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Unit Barang</th>
                    <th>Peminjam</th>
                    <th>Tgl Pinjam</th>
                    <th>Estimasi Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamans as $p)
                <tr>
                    <td>
                        <strong>{{ $p->unitBarang->kode_unit }}</strong><br>
                        <small style="color: var(--text-muted)">{{ $p->unitBarang->barang->nama }}</small>
                    </td>
                    <td>{{ $p->peminjam }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>
                        @php
                            $estimasi = \Carbon\Carbon::parse($p->estimasi_kembali);
                            $isLate = $p->status == 'Dipinjam' && $estimasi->isPast() && !$estimasi->isToday();
                        @endphp
                        <span style="{{ $isLate ? 'color: var(--danger); font-weight: bold;' : '' }}">
                            {{ $estimasi->format('d M Y') }}
                            @if($isLate) <i class="fa-solid fa-triangle-exclamation"></i> @endif
                        </span>
                    </td>
                    <td>
                        @if($p->status == 'Dipinjam')
                            <span class="badge badge-warning">Sedang Dipinjam</span>
                        @else
                            <span class="badge badge-success">Dikembalikan</span><br>
                            <small>{{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d M Y') }}</small>
                        @endif
                    </td>
                    <td>
                        @if($p->status == 'Dipinjam')
                            <form action="{{ route('peminjaman.return', $p->id) }}" method="POST" id="formReturn-{{ $p->id }}">
                                @csrf
                                <button type="button" class="btn btn-success btn-return" data-id="{{ $p->id }}" data-kode="{{ $p->unitBarang->kode_unit }}" data-peminjam="{{ $p->peminjam }}" style="padding: 0.35rem 0.75rem;">
                                    <i class="fa-solid fa-check"></i> Terima Barang
                                </button>
                            </form>
                        @else
                            <button class="btn btn-outline" style="padding: 0.35rem 0.75rem;" disabled>
                                Selesai
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted);">
                        Belum ada data peminjaman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pinjam Barang -->
<div id="pinjamModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; border-radius: 8px; width: 90%; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <form action="{{ route('peminjaman.store') }}" method="POST">
            @csrf
            
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.1rem; color: var(--primary);"><i class="fa-solid fa-hand-holding-hand"></i> Form Pinjam Barang</h3>
                <span onclick="closePinjamModal()" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            </div>
            
            <div id="pinjamModalInfo" style="padding: 1rem 1.5rem 0 1.5rem;"></div>
            
            <div style="padding: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Pilih Unit Barang</label>
                    <select name="unit_barang_id" id="unit_barang_select" class="form-control" style="width: 100%;" required>
                        <option value="">-- Pilih Unit (Hanya yg Tersedia) --</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->kode_unit }} - {{ $unit->barang->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Peminjam</label>
                    <input type="text" name="peminjam" class="form-control" placeholder="Contoh: Budi (Guru Olahraga)" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Estimasi Kembali</label>
                    <input type="date" name="estimasi_kembali" class="form-control" required min="{{ date('Y-m-d') }}">
                </div>
            </div>
            
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 0.5rem; background: var(--bg-color);">
                <button type="button" class="btn btn-outline" onclick="closePinjamModal()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Catat Pinjaman</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select2
    $('#unit_barang_select').select2({
        dropdownParent: $('#pinjamModal')
    });

    const returnButtons = document.querySelectorAll('.btn-return');
    returnButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const kode = this.getAttribute('data-kode');
            const peminjam = this.getAttribute('data-peminjam');
            
            Swal.fire({
                title: 'Konfirmasi Pengembalian',
                html: "Barang <strong>" + kode + "</strong> telah dikembalikan oleh <strong>" + peminjam + "</strong>?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Terima!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formReturn-' + id).submit();
                }
            });
        });
    });
});

function openPinjamModal() {
    document.getElementById('pinjamModal').style.display = 'block';
}

function closePinjamModal() {
    document.getElementById('pinjamModal').style.display = 'none';
}

// Close if clicking outside
window.onclick = function(event) {
    let modal = document.getElementById('pinjamModal');
    if (event.target == modal) {
        closePinjamModal();
    }
}

function startScannerForPeminjaman() {
    openScanner(function(decodedText) {
        fetch(`/api/scan-unit/${encodeURIComponent(decodedText)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.kondisi === 'Rusak Berat') {
                        toastr.error('Aset dalam kondisi Rusak Berat dan tidak dapat dipinjam.');
                        return;
                    }
                    if (data.data.status !== 'Tersedia') {
                        toastr.error(`Aset saat ini berstatus: ${data.data.status}.`);
                        return;
                    }
                    
                    let select = $('#unit_barang_select');
                    let optionExists = select.find(`option[value="${data.data.id}"]`).length > 0;
                    
                    if (!optionExists) {
                        let newOption = new Option(`${data.data.kode_unit} - ${data.data.barang.nama}`, data.data.id, true, true);
                        select.append(newOption).trigger('change');
                    } else {
                        select.val(data.data.id).trigger('change');
                    }
                    
                    let currentRoom = data.data.ruangan ? data.data.ruangan.nama : '-';
                    document.getElementById('pinjamModalInfo').innerHTML = `
                        <div style="background: #e0f2fe; padding: 1rem; border-radius: 8px; border: 1px solid #bae6fd;">
                            <div style="font-weight: bold; margin-bottom: 0.5rem;"><i class="fa-solid fa-qrcode"></i> Aset Terpilih (Hasil Scan):</div>
                            <div><strong>Kode:</strong> ${data.data.kode_unit}</div>
                            <div><strong>Barang:</strong> ${data.data.barang.nama}</div>
                            <div><strong>Ruangan:</strong> ${currentRoom}</div>
                        </div>
                    `;
                    
                    openPinjamModal();
                    toastr.success('Barang ditemukan. Silakan lengkapi form peminjaman.');
                } else {
                    toastr.error(data.message);
                }
            })
            .catch(error => {
                toastr.error('Terjadi kesalahan sistem saat mencari aset.');
            });
    });
}
</script>

@include('components.scanner-modal')
<style>
/* Adjust select2 inside modal */
.select2-container .select2-selection--single {
    height: 38px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px;
    color: var(--text-main);
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}
</style>
@endpush
@endsection
