<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitBarang;
use App\Models\Ruangan;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\MutasiBarang;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Utama
        $totalAset = UnitBarang::where('status', '!=', 'Dihapus')->count();
        $asetBaik = UnitBarang::where('status', '!=', 'Dihapus')->where('kondisi', 'Baik')->count();
        $asetRusak = UnitBarang::where('status', '!=', 'Dihapus')->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat'])->count();
        
        $totalRuangan = Ruangan::count();
        $totalKategori = Kategori::count();
        
        $peminjamanAktif = Peminjaman::where('status', 'Dipinjam')->count();
        
        // Data Grafik (Aset Berdasarkan Kondisi)
        $kondisiData = [
            'Baik' => $asetBaik,
            'Rusak Ringan' => UnitBarang::where('status', '!=', 'Dihapus')->where('kondisi', 'Rusak Ringan')->count(),
            'Rusak Berat' => UnitBarang::where('status', '!=', 'Dihapus')->where('kondisi', 'Rusak Berat')->count(),
        ];

        // Aktivitas Terbaru (Mutasi, Keluar, Peminjaman digabung)
        // Kita ambil beberapa data terbaru secara terpisah lalu di view kita tampilkan
        
        $recentPeminjaman = Peminjaman::with(['unitBarang.barang'])->latest()->take(5)->get();
        $recentMutasi = MutasiBarang::with(['unitBarang.barang', 'ruanganAsal', 'ruanganTujuan'])->latest()->take(5)->get();
        $recentKeluar = BarangKeluar::with(['unitBarang.barang'])->latest('tanggal_keluar')->take(5)->get();

        return view('dashboard.index', compact(
            'totalAset', 'asetBaik', 'asetRusak', 'totalRuangan', 'totalKategori', 'peminjamanAktif',
            'kondisiData', 'recentPeminjaman', 'recentMutasi', 'recentKeluar'
        ));
    }
}
