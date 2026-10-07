@extends('layouts.app')
@section('title', 'Master Kategori')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
    
    <!-- Form Tambah -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-plus"></i> Tambah Kategori
            </div>
        </div>
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Mebel" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Aset</label>
                <select name="tipe" class="form-control" required>
                    <option value="aset_tetap">Aset Tetap (Misal: Meja, PC)</option>
                    <option value="habis_pakai">Habis Pakai (Misal: ATK)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <i class="fa-solid fa-save"></i> Simpan Kategori
            </button>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="card-title">
                <i class="fa-solid fa-tags"></i> Daftar Kategori
            </div>
            <div>
                <input type="text" class="form-control table-search" placeholder="Cari kategori..." style="width: 200px;">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Tipe</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $index => $kat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $kat->nama }}</strong></td>
                        <td>
                            @if($kat->tipe == 'aset_tetap')
                                <span class="badge badge-success">Aset Tetap</span>
                            @else
                                <span class="badge badge-warning">Habis Pakai</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('kategori.destroy', $kat->id) }}" method="POST" id="formDeleteKategori-{{ $kat->id }}" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline" style="padding: 0.25rem 0.5rem;" onclick="editKategori({{ $kat->id }}, '{{ $kat->nama }}', '{{ $kat->tipe }}')">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-delete-kategori" data-id="{{ $kat->id }}" data-nama="{{ $kat->nama }}" style="padding: 0.25rem 0.5rem; background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3);">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted);">Belum ada kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 400px; max-width: 90%;">
        <div class="card-header">
            <div class="card-title">Edit Kategori</div>
            <button type="button" class="btn btn-outline" style="padding: 0.2rem 0.5rem; border:none;" onclick="document.getElementById('editModal').style.display='none'"><i class="fa-solid fa-times"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="nama" id="editNama" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Aset</label>
                <select name="tipe" id="editTipe" class="form-control" required>
                    <option value="aset_tetap">Aset Tetap</option>
                    <option value="habis_pakai">Habis Pakai</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Update Kategori</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editKategori(id, nama, tipe) {
    document.getElementById('editForm').action = '/kategori/' + id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editTipe').value = tipe;
    document.getElementById('editModal').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete-kategori');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            
            Swal.fire({
                title: 'Hapus Kategori?',
                html: "Anda yakin ingin menghapus <strong>" + nama + "</strong>?<br>Data yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formDeleteKategori-' + id).submit();
                }
            });
        });
    });
});
</script>
@endpush
@endsection
