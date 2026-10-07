@extends('layouts.app')
@section('title', 'Dashboard & Manajemen Aset')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $barangs->count() }}</h3>
            <p>Total Jenis Aset</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fa-solid fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $barangs->sum('total_stok') }}</h3>
            <p>Total Unit Barang</p>
        </div>
    </div>
</div>

<!-- Tombol Action -->
<div style="margin-bottom: 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem;">
    <button class="btn btn-outline" onclick="document.getElementById('modalImport').style.display='flex'">
        <i class="fa-solid fa-file-excel" style="color: #10b981;"></i> Import Excel
    </button>
    <button class="btn btn-primary" onclick="document.getElementById('modalTambah').style.display='flex'">
        <i class="fa-solid fa-plus"></i> Tambah Barang Baru
    </button>
</div>

<!-- Modal Tambah Barang -->
<div id="modalTambah" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 800px; max-width: 95%; max-height: 90vh; overflow-y: auto; margin-bottom: 0;">
        <div class="card-header" style="position: sticky; top: -1.5rem; background: var(--card-bg); z-index: 2; margin-top: -1.5rem; padding-top: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <div class="card-title" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-layer-group"></i> Form Pendaftaran Aset Baru
                <button type="button" class="btn btn-outline" style="padding: 0.1rem 0.4rem; font-size: 0.75rem; border-radius: 50%; height: 24px; width: 24px;" onclick="document.getElementById('modalHelp').style.display='flex'" title="Kapan harus input batch / sendiri?">
                    <i class="fa-solid fa-question"></i>
                </button>
            </div>
            <button type="button" class="btn btn-outline" style="padding: 0.2rem 0.5rem; border:none;" onclick="document.getElementById('modalTambah').style.display='none'"><i class="fa-solid fa-times"></i></button>
        </div>
        <div style="padding-top: 1rem;">
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">
                Anda dapat menambahkan 1 unit barang saja atau membuat banyak unit sekaligus (Bulk Generate) dengan mengisi field "Jumlah Unit".
            </p>
            <form action="{{ route('aset.bulk') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Kursi Siswa" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }} ({{ $kategori->tipe }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Merk (Opsional)</label>
                        <input type="text" name="merk" class="form-control" placeholder="Contoh: Olympic">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-control" placeholder="Contoh: Unit" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Ruangan Penempatan</label>
                        <select name="ruangan_id" class="form-control" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($ruangans as $ruang)
                                <option value="{{ $ruang->id }}">{{ $ruang->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sumber Dana</label>
                        <select name="sumber_dana_id" class="form-control" required>
                            <option value="">-- Pilih Sumber Dana --</option>
                            @foreach($sumberDanas as $sumber)
                                <option value="{{ $sumber->id }}">{{ $sumber->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun Perolehan (Opsional)</label>
                        <input type="number" name="tahun_perolehan" class="form-control" placeholder="Contoh: {{ date('Y') }}" min="1900" max="2100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Satuan (Rp) (Opsional)</label>
                        <input type="number" name="harga_perolehan" class="form-control" placeholder="Contoh: 1500000" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kondisi Awal</label>
                        <select name="kondisi" class="form-control" required>
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Unit (Isi 1 jika satuan)</label>
                        <input type="number" name="jumlah" class="form-control" placeholder="Maks: 500" required min="1" max="500" value="1">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Prefix Kode / Nomor Seri</label>
                        <input type="text" name="prefix" class="form-control" placeholder="Contoh: KRS-7A" required>
                    </div>
                </div>
                <div class="form-group" style="text-align: right; margin-bottom: 0; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Simpan & Generate Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div id="modalImport" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 500px; max-width: 95%;">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
            <div class="card-title" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-file-excel" style="color: #10b981;"></i> Import Data Barang
            </div>
            <button type="button" class="btn btn-outline" style="padding: 0.2rem 0.5rem; border:none;" onclick="document.getElementById('modalImport').style.display='none'"><i class="fa-solid fa-times"></i></button>
        </div>
        <div style="padding-top: 0.5rem;">
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">
                Silakan download template Excel terlebih dahulu, isi data barang yang ingin ditambahkan, lalu upload kembali ke sistem.
            </p>
            <div style="margin-bottom: 1.5rem;">
                <a href="{{ route('aset.import.template') }}" class="btn btn-outline" style="width: 100%; justify-content: center; border-style: dashed;">
                    <i class="fa-solid fa-download"></i> Download Template Excel
                </a>
            </div>
            
            <form action="{{ route('aset.import.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600;">Upload File Excel</label>
                    <div class="drag-drop-zone" id="dragDropZone" onclick="document.getElementById('fileInput').click()">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2.5rem; color: #10b981; margin-bottom: 0.8rem;"></i>
                        <p style="margin: 0; font-weight: 500; color: var(--text-color);">Klik atau Drag & Drop file Excel ke sini</p>
                        <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted); margin-top: 0.3rem;" id="fileNameDisplay">Maksimal 5MB (Format: .xlsx, .csv)</p>
                    </div>
                    <input type="file" name="file" id="fileInput" accept=".xlsx, .xls, .csv" required style="display: none;" onchange="updateFileName(this)">
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modalImport').style.display='none'">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-upload"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div class="card-title">
            <i class="fa-solid fa-list"></i> Daftar Induk Barang
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="text" class="form-control table-search" placeholder="Cari barang, merk, kategori..." style="width: 250px; max-width: 100%;">
            <a href="{{ route('aset.export') }}" class="btn btn-outline" style="color: #10b981; border-color: #10b981;">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Merk</th>
                    <th>Total Unit</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangs as $barang)
                <tr>
                    <td><strong>{{ $barang->nama }}</strong></td>
                    <td><span class="badge badge-primary">{{ $barang->kategori->nama }}</span></td>
                    <td>{{ $barang->merk ?? '-' }}</td>
                    <td>
                        <span class="badge badge-success">{{ $barang->total_stok }} Unit</span>
                    </td>
                    <td>{{ $barang->satuan }}</td>
                    <td>
                        <div style="display: flex; gap: 0.25rem;">
                            <a href="{{ route('aset.show', $barang->id) }}" class="btn btn-outline" style="padding: 0.35rem 0.75rem;" title="Detail Unit">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <button class="btn btn-outline" style="padding: 0.35rem 0.75rem; color: #f59e0b; border-color: #f59e0b;" onclick="openEditBarangModal({{ $barang->toJson() }})" title="Edit Data Induk">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <form action="{{ route('aset.destroy', $barang->id) }}" method="POST" id="formDeleteBarang-{{ $barang->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline btn-delete-barang" data-id="{{ $barang->id }}" data-nama="{{ $barang->nama }}" style="padding: 0.35rem 0.75rem; color: #ef4444; border-color: #ef4444;" title="Hapus Data Induk">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted);">
                        Belum ada data barang. Silakan input dari form di atas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($barangs->hasPages())
    <div style="padding: 1rem; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
        {{ $barangs->links() }}
    </div>
    @endif
</div>

<!-- Modal Help / Panduan Input -->
<div id="modalHelp" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 105; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 500px; max-width: 90%;">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); margin-bottom: 1rem; padding-bottom: 1rem;">
            <div class="card-title" style="color: var(--primary);">
                <i class="fa-solid fa-circle-info"></i> Panduan Input: Sendiri vs Massal
            </div>
            <button type="button" class="btn btn-outline" style="padding: 0.2rem 0.5rem; border:none;" onclick="document.getElementById('modalHelp').style.display='none'"><i class="fa-solid fa-times"></i></button>
        </div>
        <div style="font-size: 0.9rem; color: var(--text-main); line-height: 1.6;">
            <p><strong>Kapan harus input SATU PERSATU?</strong></p>
            <p style="margin-bottom: 1rem; color: var(--text-muted);">Jika barang yang diinput memiliki spesifikasi atau merk yang <strong>berbeda</strong>. Contoh: Anda mendaftarkan 1 Laptop Asus dan 1 Laptop Acer. Maka masukkan 2 kali dengan "Jumlah Unit: 1" untuk masing-masing merk.</p>
            
            <p><strong>Kapan menggunakan fitur MASSAL (Bulk Generate)?</strong></p>
            <p style="margin-bottom: 1rem; color: var(--text-muted);">Jika Anda membeli/mendaftarkan barang dengan merk dan spesifikasi yang <strong>sama persis</strong> dalam jumlah banyak. Contoh: Mendaftarkan 100 Kursi Siswa merk Olympic. Maka cukup ketik Jumlah Unit: 100, dan sistem akan membuatkan 100 barcode/unit secara otomatis.</p>
            
            <div class="alert alert-info" style="margin-bottom: 0;">
                <i class="fa-solid fa-lightbulb"></i> Tips: Untuk barang elektronik (Laptop, PC, dll) disarankan diinput satu persatu agar pencatatan merk dan garansi lebih akurat.
            </div>
        </div>
        <div style="text-align: right; margin-top: 1.5rem;">
            <button type="button" class="btn btn-primary" onclick="document.getElementById('modalHelp').style.display='none'">Paham</button>
        </div>
    </div>
</div>

<!-- Modal Edit Barang -->
<div id="modalEditBarang" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 105; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 500px; max-width: 90%;">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); margin-bottom: 1rem; padding-bottom: 1rem;">
            <div class="card-title" style="color: var(--primary);">
                <i class="fa-solid fa-pen"></i> Edit Data Induk Barang
            </div>
            <button type="button" class="btn btn-outline" style="padding: 0.2rem 0.5rem; border:none;" onclick="document.getElementById('modalEditBarang').style.display='none'"><i class="fa-solid fa-times"></i></button>
        </div>
        <form id="formEditBarang" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="kategori_id" id="edit_kategori_id" class="form-control" required>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Merk / Type</label>
                <input type="text" name="merk" id="edit_merk" class="form-control">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="satuan" id="edit_satuan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun Perolehan</label>
                    <input type="number" name="tahun_perolehan" id="edit_tahun_perolehan" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="edit_tanggal_masuk" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Satuan (Rp)</label>
                    <input type="number" name="harga_perolehan" id="edit_harga_perolehan" class="form-control">
                </div>
            </div>
            
            <div style="margin-top: 1.5rem; text-align: right;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEditBarang').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditBarangModal(barang) {
        document.getElementById('formEditBarang').action = `/aset/${barang.id}`;
        document.getElementById('edit_nama').value = barang.nama;
        document.getElementById('edit_kategori_id').value = barang.kategori_id;
        document.getElementById('edit_merk').value = barang.merk || '';
        document.getElementById('edit_satuan').value = barang.satuan;
        document.getElementById('edit_tahun_perolehan').value = barang.tahun_perolehan || '';
        document.getElementById('edit_tanggal_masuk').value = barang.tanggal_masuk || '';
        document.getElementById('edit_harga_perolehan').value = barang.harga_perolehan || '';
        document.getElementById('modalEditBarang').style.display = 'flex';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete-barang');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const barangId = this.getAttribute('data-id');
                const namaBarang = this.getAttribute('data-nama');
                
                Swal.fire({
                    title: 'Hapus Data Induk?',
                    html: "Data induk <strong>" + namaBarang + "</strong> beserta <strong>SELURUH UNITNYA</strong> akan dihapus permanen!<br><br>Ini tidak bisa dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formDeleteBarang-' + barangId).submit();
                    }
                });
            });
        });
    });
</script>

<style>
    .drag-drop-zone {
        border: 2px dashed #10b981;
        background-color: #f0fdf4;
        border-radius: 8px;
        padding: 2.5rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .drag-drop-zone:hover, .drag-drop-zone.dragover {
        background-color: #d1fae5;
        border-color: #059669;
    }
</style>

<script>
    const zone = document.getElementById('dragDropZone');
    const fileInput = document.getElementById('fileInput');
    const display = document.getElementById('fileNameDisplay');

    zone.addEventListener('dragover', (e) => {
        e.preventDefault();
        zone.classList.add('dragover');
    });

    zone.addEventListener('dragleave', () => {
        zone.classList.remove('dragover');
    });

    zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateFileName(fileInput);
        }
    });

    function updateFileName(inputElement) {
        if (inputElement.files.length > 0) {
            display.textContent = 'Terpilih: ' + inputElement.files[0].name;
            display.style.color = '#059669';
            display.style.fontWeight = 'bold';
        } else {
            display.textContent = 'Maksimal 5MB (Format: .xlsx, .csv)';
            display.style.color = 'var(--text-muted)';
            display.style.fontWeight = 'normal';
        }
    }
</script>
@endsection
