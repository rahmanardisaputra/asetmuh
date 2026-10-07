<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitBarang;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $units = UnitBarang::with(['barang', 'ruangan'])
                ->where('status', '!=', 'Keluar')
                ->where('status', '!=', 'Dihapus')
                ->paginate(20);
        return view('barang_keluar.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_ids' => 'required|array',
            'unit_ids.*' => 'exists:unit_barangs,id',
            'tanggal_keluar' => 'required|date',
            'jenis' => 'required|in:Dimusnahkan,Dijual,Dihibahkan,Hilang,Lainnya',
            'keterangan' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $keluarData = [];
            foreach ($request->unit_ids as $id) {
                $keluarData[] = [
                    'unit_barang_id' => $id,
                    'tanggal_keluar' => $request->tanggal_keluar,
                    'jenis' => $request->jenis,
                    'keterangan' => $request->keterangan,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            BarangKeluar::insert($keluarData);

            UnitBarang::whereIn('id', $request->unit_ids)
                ->update(['status' => 'Dihapus']);
        });

        return redirect()->route('barang-keluar.index')
            ->with('success', count($request->unit_ids) . ' unit barang berhasil dikeluarkan/dihapus.');
    }
}
