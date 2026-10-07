<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\UnitBarang;

class PeminjamanController extends Controller
{
    public function index()
    {
        $units = UnitBarang::where('status', 'Tersedia')->get();
        $peminjamans = Peminjaman::with('unitBarang.barang')->latest()->get();
        
        return view('peminjaman.index', compact('units', 'peminjamans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_barang_id' => 'required|exists:unit_barangs,id',
            'peminjam' => 'required|string|max:255',
            'estimasi_kembali' => 'required|date|after_or_equal:today'
        ]);

        $unit = UnitBarang::findOrFail($request->unit_barang_id);
        
        if ($unit->status !== 'Tersedia') {
            return back()->withErrors(['error' => 'Unit tidak tersedia untuk dipinjam.']);
        }

        Peminjaman::create([
            'unit_barang_id' => $unit->id,
            'peminjam' => $request->peminjam,
            'tanggal_pinjam' => now(),
            'estimasi_kembali' => $request->estimasi_kembali,
            'status' => 'Dipinjam'
        ]);

        // Update unit status
        $unit->update(['status' => 'Dipinjam']);

        return redirect()->route('peminjaman.index')->with('success', 'Barang berhasil dipinjam.');
    }

    public function returnItem($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status === 'Dikembalikan') {
            return back()->withErrors(['error' => 'Barang sudah dikembalikan sebelumnya.']);
        }

        $peminjaman->update([
            'status' => 'Dikembalikan',
            'tanggal_kembali' => now()
        ]);

        $peminjaman->unitBarang->update(['status' => 'Tersedia']);

        return redirect()->route('peminjaman.index')->with('success', 'Barang berhasil dikembalikan.');
    }
}
