<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitBarang;

class KondisiController extends Controller
{
    public function index(Request $request)
    {
        $ruangans = \App\Models\Ruangan::all();
        $kategoris = \App\Models\Kategori::all();

        $query = UnitBarang::with(['barang.kategori', 'ruangan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_unit', 'like', "%{$search}%")
                  ->orWhereHas('barang', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('kategori_id')) {
            $query->whereHas('barang', function($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $units = $query->paginate(20)->appends($request->query());

        return view('kondisi.index', compact('units', 'ruangans', 'kategoris'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:unit_barangs,id',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat'
        ]);

        $unit = UnitBarang::findOrFail($request->unit_id);
        $unit->update(['kondisi' => $request->kondisi]);

        return redirect()->route('kondisi.index')
            ->with('success', 'Kondisi unit ' . $unit->kode_unit . ' berhasil diperbarui.');
    }
}
