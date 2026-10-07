<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function akun()
    {
        return view('profil.akun');
    }

    public function sekolah()
    {
        return view('profil.sekolah');
    }

    public function updateAkun(Request $request)
    {
        return back()->with('success', 'Profil akun berhasil diperbarui (Simulasi)!');
    }

    public function updateSekolah(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'sub_nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        $profil = \App\Models\ProfilSekolah::first();
        
        $data = $request->only(['nama_sekolah', 'sub_nama', 'alamat', 'email', 'telepon']);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($profil->logo && file_exists(public_path('uploads/' . $profil->logo))) {
                unlink(public_path('uploads/' . $profil->logo));
            }

            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Ensure uploads directory exists
            if (!file_exists(public_path('uploads'))) {
                mkdir(public_path('uploads'), 0777, true);
            }
            
            $file->move(public_path('uploads'), $filename);
            $data['logo'] = $filename;
        }

        $profil->update($data);

        return back()->with('success', 'Profil sekolah berhasil diperbarui!');
    }
}
