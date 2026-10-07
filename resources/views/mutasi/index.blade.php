@extends('layouts.app')
@section('title', 'Mutasi Ruangan Barang')

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
            <i class="fa-solid fa-arrow-right-arrow-left"></i> Pindahkan Unit Barang
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <form action="{{ route('mutasi.index') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center; margin: 0;">
                <select name="ruangan_id" class="form-control" onchange="this.form.submit()" style="width: 200px; margin: 0;" required>
                    <option value="">-- Pilih Ruangan Asal --</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}" {{ $ruangan_id == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
                    @endforeach
                </select>
                @if($ruangan_id)
                <a href="{{ route('mutasi.index') }}" class="btn btn-outline" style="padding: 0.45rem 0.75rem;" title="Reset Pencarian"><i class="fa-solid fa-times"></i></a>
                @endif
            </form>

            @if($ruangan_id)
            <button type="button" class="btn btn-primary" onclick="openMutasiModal()">
                <i class="fa-solid fa-truck-fast"></i> Proses Mutasi
            </button>
            <input type="text" class="form-control table-search" placeholder="Cari kode, barang..." style="width: 200px;">
            @endif
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
                    <td><span class="badge badge-warning">{{ $unit->ruangan->nama }}</span></td>
                    <td>{{ $unit->kondisi }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem 1rem;">
                        @if(!$ruangan_id)
                            <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color);"><i class="fa-solid fa-door-open"></i></div>
                            <h4 style="color: var(--text-main); margin-bottom: 0.5rem;">Silakan Pilih Ruangan</h4>
                            <p>Pilih ruangan asal terlebih dahulu untuk melihat daftar barang yang bisa dimutasi.</p>
                        @else
                            <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color);"><i class="fa-solid fa-box-open"></i></div>
                            <h4 style="color: var(--text-main); margin-bottom: 0.5rem;">Data Kosong</h4>
                            <p>Tidak ada barang yang tersedia untuk dimutasi di ruangan ini.</p>
                        @endif
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

<!-- Modal Mutasi -->
<div id="mutasiModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; border-radius: 8px; width: 90%; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <form id="mutasiForm" action="{{ route('mutasi.store') }}" method="POST">
            @csrf
            <div id="hiddenInputsContainer"></div>
            
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.1rem; color: var(--heading-color);"><i class="fa-solid fa-truck-fast"></i> Form Mutasi Barang</h3>
                <span onclick="closeMutasiModal()" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            </div>
            
            <div style="padding: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Ruangan Tujuan</label>
                    <select name="ruangan_tujuan_id" class="form-control" required>
                        <option value="">-- Pilih Ruangan Tujuan --</option>
                        @foreach($ruangans as $r)
                            <option value="{{ $r->id }}">{{ $r->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Mutasi</label>
                    <input type="date" name="tanggal_mutasi" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Keperluan / Alasan Pindah</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Misal: Dipinjam untuk acara lab, atau Pemindahan aset permanen" required></textarea>
                </div>
            </div>
            
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 0.5rem; background: var(--bg-color);">
                <button type="button" class="btn btn-outline" onclick="closeMutasiModal()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Mutasi</button>
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
                cb.closest('tr').style.backgroundColor = 'rgba(102, 126, 234, 0.05)';
            } else {
                cb.closest('tr').style.backgroundColor = '';
            }
        });
    });

    document.querySelectorAll('.unit-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            if(this.checked) {
                this.closest('tr').style.backgroundColor = 'rgba(102, 126, 234, 0.05)';
            } else {
                this.closest('tr').style.backgroundColor = '';
            }
        });
    });

    function openMutasiModal() {
        let selected = document.querySelectorAll('.unit-checkbox:checked');
        if (selected.length === 0) {
            alert('Pilih minimal 1 unit barang untuk dimutasi!');
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

        document.getElementById('mutasiModal').style.display = 'block';
    }

    function closeMutasiModal() {
        document.getElementById('mutasiModal').style.display = 'none';
    }

    // Close if clicking outside
    window.onclick = function(event) {
        let modal = document.getElementById('mutasiModal');
        if (event.target == modal) {
            closeMutasiModal();
        }
    }
</script>
@endpush
