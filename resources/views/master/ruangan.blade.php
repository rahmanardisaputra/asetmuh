@extends('layouts.app')
@section('title', 'Master Ruangan')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
    
    <!-- Form Tambah -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-plus"></i> Tambah Ruangan
            </div>
        </div>
        <form action="{{ route('ruangan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Ruangan</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Lab Komputer" required>
            </div>
            <div class="form-group">
                <label class="form-label">Penanggung Jawab (Opsional)</label>
                <input type="text" name="penanggung_jawab" class="form-control" placeholder="Contoh: Budi Santoso">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <i class="fa-solid fa-save"></i> Simpan Ruangan
            </button>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="card-title">
                <i class="fa-solid fa-door-open"></i> Daftar Ruangan
            </div>
            <div>
                <input type="text" class="form-control table-search" placeholder="Cari ruangan..." style="width: 200px;">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Ruangan</th>
                        <th>Penanggung Jawab</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ruangans as $index => $ruang)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $ruang->nama }}</strong></td>
                        <td>{{ $ruang->penanggung_jawab ?? '-' }}</td>
                        <td>
                            <form action="{{ route('ruangan.destroy', $ruang->id) }}" method="POST" id="formDeleteRuangan-{{ $ruang->id }}" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <!-- Edit via prompt for simplicity or can use modal -->
                                <button type="button" class="btn btn-outline" style="padding: 0.25rem 0.5rem;" onclick="editRuangan({{ $ruang->id }}, '{{ $ruang->nama }}', '{{ $ruang->penanggung_jawab }}')">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-delete-ruangan" data-id="{{ $ruang->id }}" data-nama="{{ $ruang->nama }}" style="padding: 0.25rem 0.5rem; background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3);">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted);">Belum ada ruangan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit (Simple hidden form) -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 400px; max-width: 90%;">
        <div class="card-header">
            <div class="card-title">Edit Ruangan</div>
            <button type="button" class="btn btn-outline" style="padding: 0.2rem 0.5rem; border:none;" onclick="document.getElementById('editModal').style.display='none'"><i class="fa-solid fa-times"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Ruangan</label>
                <input type="text" name="nama" id="editNama" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Penanggung Jawab</label>
                <input type="text" name="penanggung_jawab" id="editPJ" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Update Ruangan</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editRuangan(id, nama, pj) {
    document.getElementById('editForm').action = '/ruangan/' + id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editPJ').value = pj === '-' ? '' : pj;
    document.getElementById('editModal').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete-ruangan');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            
            Swal.fire({
                title: 'Hapus Ruangan?',
                html: "Anda yakin ingin menghapus <strong>" + nama + "</strong>?<br>Data yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formDeleteRuangan-' + id).submit();
                }
            });
        });
    });
});
</script>
@endpush
@endsection
