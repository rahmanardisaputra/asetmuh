<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\SumberDana;
use App\Models\Barang;
use App\Models\UnitBarang;
use Illuminate\Support\Facades\DB;

class AsetController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        $ruangans = Ruangan::all();
        $sumberDanas = SumberDana::all();
        $barangs = Barang::with(['unitBarangs.ruangan', 'kategori'])->latest()->paginate(15);

        return view('aset.index', compact('kategoris', 'ruangans', 'sumberDanas', 'barangs'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'merk' => 'nullable|string|max:255',
            'satuan' => 'required|string|max:50',
            'sumber_dana_id' => 'required|exists:sumber_danas,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'tahun_perolehan' => 'nullable|integer',
            'tanggal_masuk' => 'required|date',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'kondisi' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'jumlah' => 'required|integer|min:1|max:500',
            'prefix' => 'required|string|max:50'
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Parent Barang
            $barang = Barang::create([
                'nama' => $request->nama,
                'kategori_id' => $request->kategori_id,
                'merk' => $request->merk,
                'satuan' => $request->satuan,
                'tahun_perolehan' => $request->tahun_perolehan,
                'tanggal_masuk' => $request->tanggal_masuk,
                'harga_perolehan' => $request->harga_perolehan,
                'total_stok' => $request->jumlah
            ]);

            // 2. Generate Units
            $units = [];
            for ($i = 1; $i <= $request->jumlah; $i++) {
                // Generate 3 digit padding, e.g., 001, 002
                $number = str_pad($i, 3, '0', STR_PAD_LEFT);
                $kode_unit = $request->prefix . '-' . $number;

                $units[] = [
                    'barang_id' => $barang->id,
                    'ruangan_id' => $request->ruangan_id,
                    'sumber_dana_id' => $request->sumber_dana_id,
                    'kode_unit' => $kode_unit,
                    'nomor_seri' => null,
                    'kondisi' => $request->kondisi,
                    'status' => 'Tersedia',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk Insert
            UnitBarang::insert($units);

            DB::commit();
            return redirect()->route('dashboard')->with('success', $request->jumlah . ' Unit ' . $request->nama . ' berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $barang = Barang::with(['unitBarangs.ruangan', 'unitBarangs.sumberDana', 'unitBarangs.mutasiBarangs.ruanganAsal', 'unitBarangs.mutasiBarangs.ruanganTujuan', 'kategori'])->findOrFail($id);
        $ruangans = \App\Models\Ruangan::all();
        $sumberDanas = \App\Models\SumberDana::all();
        $kategoris = \App\Models\Kategori::all();
        return view('aset.show', compact('barang', 'ruangans', 'sumberDanas', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'merk' => 'nullable|string|max:255',
            'satuan' => 'required|string|max:50',
            'tahun_perolehan' => 'nullable|integer',
            'tanggal_masuk' => 'required|date',
            'harga_perolehan' => 'nullable|numeric',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->only('nama', 'kategori_id', 'merk', 'satuan', 'tahun_perolehan', 'tanggal_masuk', 'harga_perolehan'));

        return redirect()->back()->with('success', 'Data Induk Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete(); // Cascades to UnitBarang via DB schema if set, otherwise need to delete units first

        return redirect()->route('aset.index')->with('success', 'Data Induk Barang beserta unitnya berhasil dihapus.');
    }

    public function updateUnit(Request $request, $id)
    {
        $request->validate([
            'kode_unit' => 'required|string|max:255|unique:unit_barangs,kode_unit,' . $id,
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'sumber_dana_id' => 'nullable|exists:sumber_danas,id',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:Tersedia,Dipinjam,Dihapus',
        ]);

        $unit = UnitBarang::findOrFail($id);
        $unit->update($request->only('kode_unit', 'ruangan_id', 'sumber_dana_id', 'kondisi', 'status'));

        return redirect()->back()->with('success', 'Data Unit Barang berhasil diperbarui.');
    }

    public function destroyUnit($id)
    {
        $unit = UnitBarang::findOrFail($id);
        $unit->delete();

        return redirect()->back()->with('success', 'Unit Barang berhasil dihapus.');
    }
    public function export()
    {
        $units = UnitBarang::with(['barang.kategori', 'ruangan', 'sumberDana'])->get();
        $fileName = 'Master_Aset_Muh1_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Kode Unit', 'Nama Barang', 'Kategori', 'Merk', 'Tahun Perolehan', 'Tanggal Masuk', 'Harga', 'Ruangan', 'Kondisi', 'Status', 'Sumber Dana'];

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
                    $unit->barang->merk ?? '-',
                    $unit->barang->tahun_perolehan ?? '-',
                    $unit->barang->tanggal_masuk ?? '-',
                    $unit->barang->harga_perolehan ?? '-',
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

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_barang.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Write heading row
            fputcsv($file, [
                'nama_barang', 'kategori', 'merk', 'satuan', 'tahun_perolehan', 
                'tanggal_masuk', 'harga_perolehan', 'jumlah', 'prefix_kode', 
                'ruangan', 'sumber_dana'
            ]);
            // Example row
            fputcsv($file, [
                'Laptop Asus', 'Elektronik', 'Asus', 'Unit', '2023', 
                '2023-10-15', '15000000', '5', 'LPT', 
                'Laboratorium Komputer', 'Dana BOS'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function previewImport(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            // Save file temporarily
            $path = $request->file('file')->store('temp');
            
            // Read Excel
            $data = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\BarangImport, $path);
            $rows = $data[0] ?? [];

            // Validate against DB
            $kategoris = \App\Models\Kategori::pluck('nama')->map(fn($v) => strtolower($v))->toArray();
            $ruangans = \App\Models\Ruangan::pluck('nama')->map(fn($v) => strtolower($v))->toArray();
            $sumberDanas = \App\Models\SumberDana::pluck('nama')->map(fn($v) => strtolower($v))->toArray();

            $previewData = [];
            $hasError = false;

            foreach ($rows as $row) {
                if (empty($row['nama_barang'])) continue;

                $isValidKategori = in_array(strtolower($row['kategori'] ?? ''), $kategoris);
                $isValidRuangan = in_array(strtolower($row['ruangan'] ?? ''), $ruangans);
                $isValidSumber = in_array(strtolower($row['sumber_dana'] ?? ''), $sumberDanas);

                $rowError = !$isValidKategori || !$isValidRuangan || !$isValidSumber;
                if ($rowError) $hasError = true;

                $previewData[] = [
                    'nama_barang' => $row['nama_barang'],
                    'kategori' => $row['kategori'] ?? '-',
                    'kategori_valid' => $isValidKategori,
                    'ruangan' => $row['ruangan'] ?? '-',
                    'ruangan_valid' => $isValidRuangan,
                    'sumber_dana' => $row['sumber_dana'] ?? '-',
                    'sumber_dana_valid' => $isValidSumber,
                    'merk' => $row['merk'] ?? '-',
                    'jumlah' => intval($row['jumlah'] ?? 1),
                    'harga' => $row['harga_perolehan'] ?? 0,
                    'tahun' => $row['tahun_perolehan'] ?? '-',
                    'satuan' => $row['satuan'] ?? 'Unit',
                    'prefix' => $row['prefix_kode'] ?? 'AST',
                    'tanggal_masuk' => $row['tanggal_masuk'] ?? now()->format('Y-m-d')
                ];
            }

            return view('aset.preview', compact('previewData', 'hasError', 'path'));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membaca file: ' . $e->getMessage()]);
        }
    }

    public function processImport(\Illuminate\Http\Request $request)
    {
        $path = $request->path_file;
        if (!$path || !\Illuminate\Support\Facades\Storage::exists($path)) {
            return redirect()->route('aset.index')->withErrors(['error' => 'File import tidak ditemukan.']);
        }

        try {
            $data = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\BarangImport, $path);
            $rows = $data[0] ?? [];

            DB::beginTransaction();
            $totalInserted = 0;

            foreach ($rows as $row) {
                if (empty($row['nama_barang'])) continue;

                $kategori = \App\Models\Kategori::where('nama', $row['kategori'])->first();
                $ruangan = \App\Models\Ruangan::where('nama', $row['ruangan'])->first();
                $sumberDana = \App\Models\SumberDana::where('nama', $row['sumber_dana'])->first();

                if (!$kategori || !$ruangan || !$sumberDana) {
                    throw new \Exception("Data Kategori, Ruangan, atau Sumber Dana tidak valid pada barang: " . $row['nama_barang']);
                }

                $jumlah = intval($row['jumlah'] ?? 1);
                $prefix = $row['prefix_kode'] ?? 'AST';

                $barang = Barang::create([
                    'nama' => $row['nama_barang'],
                    'kategori_id' => $kategori->id,
                    'merk' => $row['merk'] ?? null,
                    'satuan' => $row['satuan'] ?? 'Unit',
                    'tahun_perolehan' => $row['tahun_perolehan'] ?? null,
                    'tanggal_masuk' => $row['tanggal_masuk'] ?? now()->format('Y-m-d'),
                    'harga_perolehan' => $row['harga_perolehan'] ?? 0,
                    'total_stok' => $jumlah
                ]);

                $units = [];
                for ($i = 1; $i <= $jumlah; $i++) {
                    $number = str_pad($i, 3, '0', STR_PAD_LEFT);
                    $kode_unit = $prefix . '-' . $number;

                    $units[] = [
                        'barang_id' => $barang->id,
                        'ruangan_id' => $ruangan->id,
                        'sumber_dana_id' => $sumberDana->id,
                        'kode_unit' => $kode_unit,
                        'nomor_seri' => null,
                        'kondisi' => 'Baik',
                        'status' => 'Tersedia',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                UnitBarang::insert($units);
                $totalInserted += $jumlah;
            }

            DB::commit();
            \Illuminate\Support\Facades\Storage::delete($path);
            
            return redirect()->route('aset.index')->with('success', $totalInserted . ' unit barang berhasil diimport!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('aset.index')->withErrors(['error' => 'Gagal import: ' . $e->getMessage()]);
        }
    }
}
