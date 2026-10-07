<?php

namespace App\Http\Controllers;

use App\Models\SumberDana;
use Illuminate\Http\Request;

class SumberDanaController extends Controller
{
    public function index()
    {
        $sumberDanas = SumberDana::all();
        return view('master.sumber_dana', compact('sumberDanas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        SumberDana::create($request->all());
        return redirect()->route('sumber-dana.index')->with('success', 'Sumber Dana berhasil ditambahkan.');
    }

    public function update(Request $request, SumberDana $sumberDana)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $sumberDana->update($request->all());
        return redirect()->route('sumber-dana.index')->with('success', 'Sumber Dana berhasil diperbarui.');
    }

    public function destroy(SumberDana $sumberDana)
    {
        $sumberDana->delete();
        return redirect()->route('sumber-dana.index')->with('success', 'Sumber Dana berhasil dihapus.');
    }
}
