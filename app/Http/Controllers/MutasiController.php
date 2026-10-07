<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitBarang;
use App\Models\Ruangan;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $ruangans = Ruangan::all();
        $ruangan_id = $request->query('ruangan_id');

        if ($ruangan_id) {
            $units = UnitBarang::with(['barang', 'ruangan'])
                ->where('status', 'Tersedia')
                ->where('ruangan_id', $ruangan_id)
                ->paginate(20);
            $units->appends(['ruangan_id' => $ruangan_id]);
        } else {
            $units = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('mutasi.index', compact('ruangans', 'units', 'ruangan_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_ids' => 'required|array',
            'unit_ids.*' => 'exists:unit_barangs,id',
            'ruangan_tujuan_id' => 'required|exists:ruangans,id',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'required|string'
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $units = UnitBarang::whereIn('id', $request->unit_ids)->get();
            
            $historyData = [];
            foreach ($units as $unit) {
                $historyData[] = [
                    'unit_barang_id' => $unit->id,
                    'ruangan_asal_id' => $unit->ruangan_id,
                    'ruangan_tujuan_id' => $request->ruangan_tujuan_id,
                    'tanggal_mutasi' => $request->tanggal_mutasi,
                    'keterangan' => $request->keterangan,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            \App\Models\MutasiBarang::insert($historyData);
            
            UnitBarang::whereIn('id', $request->unit_ids)
                ->update(['ruangan_id' => $request->ruangan_tujuan_id]);
        });

        return redirect()->route('mutasi.index')
            ->with('success', count($request->unit_ids) . ' unit berhasil dimutasi dan riwayat dicatat.');
    }
}
