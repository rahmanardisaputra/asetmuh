@extends('layouts.app')
@section('title', 'Detail Barang: ' . $barang->nama)

@section('content')
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-box-open"></i> Informasi Induk Barang
        </div>
        <div>
            <a href="{{ route('aset.index') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <div>
            <small style="color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Kategori</small>
            <span class="badge badge-primary">{{ $barang->kategori->nama }}</span>
        </div>
        <div>
            <small style="color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Merk</small>
            <strong>{{ $barang->merk ?? '-' }}</strong>
        </div>
        <div>
            <small style="color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Tahun Perolehan</small>
            <strong>{{ $barang->tahun_perolehan ?? '-' }}</strong>
        </div>
        <div>
            <small style="color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Tanggal Masuk</small>
            <strong>{{ $barang->tanggal_masuk ? \Carbon\Carbon::parse($barang->tanggal_masuk)->format('d F Y') : '-' }}</strong>
        </div>
        <div>
            <small style="color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Harga Perolehan</small>
            <strong>{{ $barang->harga_perolehan ? 'Rp ' . number_format($barang->harga_perolehan, 0, ',', '.') : '-' }}</strong>
        </div>
        <div>
            <small style="color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Total Unit Tersedia</small>
            <strong>{{ $barang->unitBarangs->where('status', 'Tersedia')->count() }} / {{ $barang->total_stok }} {{ $barang->satuan }}</strong>
        </div>
        <div>
            <small style="color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Kondisi Baik</small>
            <strong>{{ $barang->unitBarangs->where('kondisi', 'Baik')->count() }} Unit</strong>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div class="card-title">
            <i class="fa-solid fa-list-ol"></i> Rincian Seluruh Unit
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="text" class="form-control table-search" placeholder="Cari kode, ruangan..." style="width: 250px;">
            <button onclick="preparePrint()" class="btn btn-primary" title="Cetak Stiker Label Barcode">
                <i class="fa-solid fa-print"></i> Cetak Label Terpilih
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="checkAll" onchange="toggleCheckboxes(this)">
                    </th>
                    <th>No</th>
                    <th>Kode Unit</th>
                    <th>Ruangan (Posisi)</th>
                    <th>Sumber Dana</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barang->unitBarangs as $index => $unit)
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" class="unit-checkbox" value="{{ $unit->id }}">
                    </td>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $unit->kode_unit }}</strong></td>
                    <td>{{ $unit->ruangan->nama ?? '-' }}</td>
                    <td>{{ $unit->sumberDana->nama ?? '-' }}</td>
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
                        @if($unit->status == 'Tersedia')
                            <span class="badge badge-primary">{{ $unit->status }}</span>
                        @else
                            <span class="badge badge-warning">{{ $unit->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; justify-content: center;">
                            <button type="button" class="btn btn-outline btn-sm" onclick="toggleHistory({{ $unit->id }})" title="Lihat Kartu Barang (Riwayat Mutasi)">
                                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat
                            </button>
                            <button type="button" class="btn btn-outline btn-sm" style="color: #f59e0b; border-color: #f59e0b;" onclick='openEditUnitModal(@json($unit))' title="Edit Unit Barang">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <form action="{{ route('aset.unit.destroy', $unit->id) }}" method="POST" id="formDeleteUnit-{{ $unit->id }}" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline btn-sm btn-delete-unit" data-id="{{ $unit->id }}" data-kode="{{ $unit->kode_unit }}" style="color: #ef4444; border-color: #ef4444;" title="Hapus Unit Barang">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr id="history-{{ $unit->id }}" class="history-row" style="display: none; background-color: #f8f9fa;">
                    <td colspan="8" style="padding: 1rem 2rem;">
                        <div style="font-weight: bold; margin-bottom: 0.5rem; color: var(--primary-color);">
                            <i class="fa-solid fa-timeline"></i> Riwayat Mutasi (Kartu Barang)
                        </div>
                        @if($unit->mutasiBarangs->count() > 0)
                            <table class="table" style="background: white; border: 1px solid var(--border-color); margin: 0;">
                                <thead>
                                    <tr>
                                        <th style="background: #f1f5f9;">Tanggal Pindah</th>
                                        <th style="background: #f1f5f9;">Dari Ruangan</th>
                                        <th style="background: #f1f5f9;">Ke Ruangan</th>
                                        <th style="background: #f1f5f9;">Keperluan / Alasan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unit->mutasiBarangs as $mutasi)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->format('d M Y') }}</td>
                                        <td>{{ $mutasi->ruanganAsal ? $mutasi->ruanganAsal->nama : 'Data Awal' }}</td>
                                        <td><strong>{{ $mutasi->ruanganTujuan->nama }}</strong></td>
                                        <td>{{ $mutasi->keterangan }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div style="color: var(--text-muted); font-style: italic;">
                                Belum ada riwayat mutasi untuk unit ini.
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Layout khusus untuk Cetak Label Barcode -->
<div class="print-labels">
    @foreach($barang->unitBarangs as $unit)
    @php
        $qrData = "Kode: {$unit->kode_unit}\n";
        $qrData .= "Barang: {$barang->nama}\n";
        $qrData .= "Merk: " . ($barang->merk ?? '-') . "\n";
        $qrData .= "Kategori: " . ($barang->kategori ? $barang->kategori->nama : '-') . "\n";
        $qrData .= "Sumber Dana: " . ($unit->sumberDana->nama ?? '-') . "\n";
        $qrData .= "Tahun: " . ($barang->tahun_perolehan ?? '-');
    @endphp
    <div class="label-box" id="label-{{ $unit->id }}">
        <!-- Left side: QR Code -->
        <div style="width: 35%; display: flex; align-items: center; justify-content: center; border-right: 1px solid black; padding: 0.2rem;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($qrData) }}" alt="QR Code" style="width: 100%; height: auto; max-width: 80px;">
        </div>

        <!-- Right side: Text details -->
        <div style="width: 65%; display: flex; flex-direction: column; justify-content: flex-start;">
            <!-- Header -->
            <div style="font-size: 0.65rem; font-weight: bold; border-bottom: 1px solid black; padding: 0.3rem 0; text-transform: uppercase; text-align: center;">
                ASET {{ $profil_sekolah->nama_sekolah ?? 'SEKOLAH' }}
            </div>
            
            <!-- Details Table -->
            <table style="width: 100%; border-collapse: collapse; font-size: 0.6rem; margin: 0; text-align: left; font-family: sans-serif; line-height: 1.1;">
                <tr>
                    <td style="border-bottom: 1px solid black; border-right: 1px solid black; padding: 0.05rem 0.2rem; width: 30%;">Kode</td>
                    <td style="border-bottom: 1px solid black; padding: 0.05rem 0.2rem; width: 70%;">: {{ $unit->kode_unit }}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid black; border-right: 1px solid black; padding: 0.05rem 0.2rem;">Barang</td>
                    <td style="border-bottom: 1px solid black; padding: 0.05rem 0.2rem;">: {{ $barang->nama }}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid black; border-right: 1px solid black; padding: 0.05rem 0.2rem;">Merk</td>
                    <td style="border-bottom: 1px solid black; padding: 0.05rem 0.2rem;">: {{ $barang->merk ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid black; border-right: 1px solid black; padding: 0.05rem 0.2rem;">Kategori</td>
                    <td style="border-bottom: 1px solid black; padding: 0.05rem 0.2rem;">: {{ $barang->kategori ? $barang->kategori->nama : '-' }}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid black; border-right: 1px solid black; padding: 0.05rem 0.2rem;">Sumber dana</td>
                    <td style="border-bottom: 1px solid black; padding: 0.05rem 0.2rem;">: {{ $unit->sumberDana ? $unit->sumberDana->nama : '-' }}</td>
                </tr>
                <tr>
                    <td style="border-right: 1px solid black; padding: 0.05rem 0.2rem;">Tahun</td>
                    <td style="padding: 0.05rem 0.2rem;">: {{ $barang->tahun_perolehan ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
    @endforeach
</div>

<style>
@media screen {
    .print-labels { display: none; }
}
@media print {
    body * {
        visibility: hidden;
    }
    .history-row {
        display: none !important;
    }
    .print-labels, .print-labels * {
        visibility: visible;
    }
    .print-labels {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    .label-box {
        display: none; /* Sembunyikan semua secara default */
        border: 1px solid black;
        border-radius: 8px;
        page-break-inside: avoid;
        background: white;
        color: black;
        overflow: hidden;
    }
    .label-box.show-print {
        display: flex; /* Munculkan hanya yang dipilih */
        flex-direction: row;
    }
}
</style>

@push('scripts')
<script>
function toggleCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.unit-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
}

function preparePrint() {
    const checkboxes = document.querySelectorAll('.unit-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Pilih setidaknya satu unit barang untuk dicetak labelnya.');
        return;
    }

    // Reset all labels
    document.querySelectorAll('.label-box').forEach(box => {
        box.classList.remove('show-print');
    });

    // Tampilkan label yang dipilih
    checkboxes.forEach(cb => {
        const labelBox = document.getElementById('label-' + cb.value);
        if (labelBox) {
            labelBox.classList.add('show-print');
        }
    });

    // Panggil dialog print
    window.print();
}

function toggleHistory(id) {
    const row = document.getElementById('history-' + id);
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
    } else {
        row.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete-unit');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const unitId = this.getAttribute('data-id');
            const kodeUnit = this.getAttribute('data-kode');
            
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data unit " + kodeUnit + " akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formDeleteUnit-' + unitId).submit();
                }
            });
        });
    });
});
</script>

<!-- Modal Edit Unit -->
<div id="modalEditUnit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 105; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 500px; max-width: 90%;">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); margin-bottom: 1rem; padding-bottom: 1rem;">
            <div class="card-title" style="color: var(--primary);">
                <i class="fa-solid fa-pen"></i> Edit Unit Barang
            </div>
            <button type="button" class="btn btn-outline" style="padding: 0.2rem 0.5rem; border:none;" onclick="document.getElementById('modalEditUnit').style.display='none'"><i class="fa-solid fa-times"></i></button>
        </div>
        <form id="formEditUnit" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Kode Unit</label>
                <input type="text" name="kode_unit" id="edit_kode_unit" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Ruangan</label>
                <select name="ruangan_id" id="edit_ruangan_id" class="form-control">
                    <option value="">Pilih Ruangan</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Sumber Dana</label>
                <select name="sumber_dana_id" id="edit_sumber_dana_id" class="form-control">
                    <option value="">Pilih Sumber Dana</option>
                    @foreach($sumberDanas as $sd)
                        <option value="{{ $sd->id }}">{{ $sd->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kondisi</label>
                    <select name="kondisi" id="edit_kondisi" class="form-control" required>
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="edit_status" class="form-control" required>
                        <option value="Tersedia">Tersedia</option>
                        <option value="Dipinjam">Dipinjam</option>
                        <option value="Dihapus">Dihapus</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-top: 1.5rem; text-align: right;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEditUnit').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditUnitModal(unit) {
        document.getElementById('formEditUnit').action = `/aset/unit/${unit.id}`;
        document.getElementById('edit_kode_unit').value = unit.kode_unit;
        document.getElementById('edit_ruangan_id').value = unit.ruangan_id || '';
        document.getElementById('edit_sumber_dana_id').value = unit.sumber_dana_id || '';
        document.getElementById('edit_kondisi').value = unit.kondisi;
        document.getElementById('edit_status').value = unit.status;
        document.getElementById('modalEditUnit').style.display = 'flex';
    }
</script>
@endpush
@endsection
