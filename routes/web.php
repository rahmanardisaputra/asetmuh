<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AsetController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\PeminjamanController;

use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/api/scan-unit/{kode}', function($kode) {
    $unit = \App\Models\UnitBarang::with(['barang', 'ruangan'])->where('kode_unit', $kode)->first();
    if ($unit) {
        return response()->json(['success' => true, 'data' => $unit]);
    }
    return response()->json(['success' => false, 'message' => 'Aset dengan kode ' . $kode . ' tidak ditemukan.']);
})->where('kode', '.*');

Route::post('/aset/bulk', [AsetController::class, 'bulkStore'])->name('aset.bulk');
Route::post('/aset/import/preview', [AsetController::class, 'previewImport'])->name('aset.import.preview');
Route::post('/aset/import/process', [AsetController::class, 'processImport'])->name('aset.import.process');
Route::get('/aset/import/template', [AsetController::class, 'downloadTemplate'])->name('aset.import.template');
Route::get('/aset', [AsetController::class, 'index'])->name('aset.index');
Route::get('/aset/{id}', [AsetController::class, 'show'])->name('aset.show');
Route::put('/aset/{id}', [AsetController::class, 'update'])->name('aset.update');
Route::delete('/aset/{id}', [AsetController::class, 'destroy'])->name('aset.destroy');
Route::put('/aset/unit/{id}', [AsetController::class, 'updateUnit'])->name('aset.unit.update');
Route::delete('/aset/unit/{id}', [AsetController::class, 'destroyUnit'])->name('aset.unit.destroy');

Route::get('/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
Route::post('/mutasi', [MutasiController::class, 'store'])->name('mutasi.store');

Route::get('/kondisi', [KondisiController::class, 'index'])->name('kondisi.index');
Route::post('/kondisi', [KondisiController::class, 'update'])->name('kondisi.update');

Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
Route::post('/peminjaman/{id}/kembali', [PeminjamanController::class, 'returnItem'])->name('peminjaman.return');

use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SumberDanaController;

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
Route::get('/laporan/keluar', [LaporanController::class, 'keluar'])->name('laporan.keluar');
Route::get('/laporan/keluar/export', [LaporanController::class, 'exportKeluar'])->name('laporan.export-keluar');
Route::get('/aset-export/csv', [AsetController::class, 'export'])->name('aset.export');

Route::resource('ruangan', RuanganController::class)->except(['create', 'show', 'edit']);
Route::resource('kategori', KategoriController::class)->except(['create', 'show', 'edit']);
Route::resource('sumber-dana', SumberDanaController::class)->except(['create', 'show', 'edit']);

use App\Http\Controllers\ProfilController;

Route::get('/profil/akun', [ProfilController::class, 'akun'])->name('profil.akun');
Route::post('/profil/akun', [ProfilController::class, 'updateAkun'])->name('profil.akun.update');

Route::get('/profil/sekolah', [ProfilController::class, 'sekolah'])->name('profil.sekolah');
Route::post('/profil/sekolah', [ProfilController::class, 'updateSekolah'])->name('profil.sekolah.update');

use App\Http\Controllers\BarangKeluarController;
Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');
Route::post('/barang-keluar', [BarangKeluarController::class, 'store'])->name('barang-keluar.store');
