@extends('layouts.app')
@section('title', 'Master Sumber Dana')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
    
    <!-- Form Tambah -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-plus"></i> Tambah Sumber Dana
            </div>
        </div>
        <form action="{{ route('sumber-dana.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Sumber Dana</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Dana BOS" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <i class="fa-solid fa-save"></i> Simpan
            </button>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="card-title">
                <i class="fa-solid fa-sack-dollar"></i> Daftar Sumber Dana
            </div>
            <div>
                <input type="text" class="form-control table-search" placeholder="Cari sumber dana..." style="width: 200px;">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Sumber Dana</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sumberDanas as $index => $sumber)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $sumber->nama }}</strong></td>
                        <td>
                            <form action="{{ route('sumber-dana.destroy', $sumber->id) }}" method="POST" id="formDeleteSumber-{{ $sumber->id }}" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline" style="padding: 0.25rem 0.5rem;" onclick="editSumber({{ $sumber->id }}, '{{ $sumber->nama }}')">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-delete-sumber" data-id="{{ $sumber->id }}" data-nama="{{ $sumber->nama }}" style="padding: 0.25rem 0.5rem; background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3);">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: var(--text-muted);">Belum ada sumber dana.</td>
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
            <div class="card-title">Edit Sumber Dana</div>
            <button type="button" class="btn btn-outline" style="padding: 0.2rem 0.5rem; border:none;" onclick="document.getElementById('editModal').style.display='none'"><i class="fa-solid fa-times"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Sumber Dana</label>
                <input type="text" name="nama" id="editNama" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Update Sumber Dana</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editSumber(id, nama) {
    document.getElementById('editForm').action = '/sumber-dana/' + id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editModal').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete-sumber');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            
            Swal.fire({
                title: 'Hapus Sumber Dana?',
                html: "Anda yakin ingin menghapus <strong>" + nama + "</strong>?<br>Data yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formDeleteSumber-' + id).submit();
                }
            });
        });
    });
});
</script>
@endpush
@endsection
