<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitBarang;
use App\Models\Ruangan;
use App\Models\Kategori;
use App\Models\SumberDana;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $ruangans = Ruangan::all();
        $kategoris = Kategori::all();
        $sumberDanas = SumberDana::all();
        // Ambil list tahun perolehan unik dari tabel barang
        $tahuns = \App\Models\Barang::whereNotNull('tahun_perolehan')
                    ->distinct()
                    ->orderBy('tahun_perolehan', 'desc')
                    ->pluck('tahun_perolehan');

        // Query builder for filtering
        $query = UnitBarang::with(['barang.kategori', 'ruangan', 'sumberDana']);

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('kategori_id')) {
            $query->whereHas('barang', function($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }
        
        if ($request->filled('tahun_perolehan')) {
            $query->whereHas('barang', function($q) use ($request) {
                $q->where('tahun_perolehan', $request->tahun_perolehan);
            });
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sumber_dana_id')) {
            $query->where('sumber_dana_id', $request->sumber_dana_id);
        }

        // Calculate Stats based on unpaginated query
        $statsQuery = clone $query;
        $stats = [
            'total' => $statsQuery->count(),
            'baik' => (clone $statsQuery)->where('kondisi', 'Baik')->count(),
            'rusak_ringan' => (clone $statsQuery)->where('kondisi', 'Rusak Ringan')->count(),
            'rusak_berat' => (clone $statsQuery)->where('kondisi', 'Rusak Berat')->count(),
            'dipinjam' => (clone $statsQuery)->where('status', 'Dipinjam')->count(),
        ];

        $units = $query->paginate(25)->appends($request->query());

        return view('laporan.index', compact('ruangans', 'kategoris', 'sumberDanas', 'tahuns', 'units', 'stats'));
    }
    public function export(Request $request)
    {
        $query = UnitBarang::with(['barang.kategori', 'ruangan', 'sumberDana']);

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }
        if ($request->filled('kategori_id')) {
            $query->whereHas('barang', function($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }
        if ($request->filled('tahun_perolehan')) {
            $query->whereHas('barang', function($q) use ($request) {
                $q->where('tahun_perolehan', $request->tahun_perolehan);
            });
        }
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('sumber_dana_id')) {
            $query->where('sumber_dana_id', $request->sumber_dana_id);
        }

        $units = $query->get();
        $fileName = 'Laporan_Aset_Muh1_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Kode Unit', 'Nama Barang', 'Kategori', 'Tahun', 'Ruangan', 'Kondisi', 'Status', 'Sumber Dana'];

        $callback = function() use($units, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $i = 1;
            foreach ($units as $unit) {
                fputcsv($file, [
                    $i++,
                    $unit->kode_unit,
                    $unit->barang->nama,
                    $unit->barang->kategori->nama ?? '-',
                    $unit->barang->tahun_perolehan ?? '-',
                    $unit->ruangan->nama ?? '-',
                    $unit->kondisi,
                    $unit->status,
                    $unit->sumberDana->nama ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function keluar(Request $request)
    {
        $query = \App\Models\BarangKeluar::with(['unitBarang.barang']);
        
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_keluar', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_akhir);
        }

        // Calculate Stats
        $statsQuery = clone $query;
        $stats = [
            'total' => $statsQuery->count(),
            'dimusnahkan' => (clone $statsQuery)->where('jenis', 'Dimusnahkan')->count(),
            'dijual' => (clone $statsQuery)->where('jenis', 'Dijual')->count(),
            'hilang' => (clone $statsQuery)->where('jenis', 'Hilang')->count(),
        ];

        $keluars = $query->orderBy('tanggal_keluar', 'desc')->paginate(25)->appends($request->query());

        return view('laporan.keluar', compact('keluars', 'stats'));
    }

    public function exportKeluar(Request $request)
    {
        $query = \App\Models\BarangKeluar::with(['unitBarang.barang']);
        
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_keluar', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_akhir);
        }

        $keluars = $query->orderBy('tanggal_keluar', 'desc')->get();
        $fileName = 'Laporan_Barang_Keluar_Muh1_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Tanggal Keluar', 'Kode Unit', 'Nama Barang', 'Jenis Pengeluaran', 'Keterangan'];

        $callback = function() use($keluars, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $i = 1;
            foreach ($keluars as $keluar) {
                fputcsv($file, [
                    $i++,
                    \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d-m-Y'),
                    $keluar->unitBarang->kode_unit ?? '-',
                    $keluar->unitBarang->barang->nama ?? '-',
                    $keluar->jenis,
                    $keluar->keterangan
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

