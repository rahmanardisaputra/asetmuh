<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@sekolah.com',
        ]);

        \App\Models\Kategori::insert([
            ['nama' => 'Mebel', 'tipe' => 'aset_tetap'],
            ['nama' => 'Elektronik', 'tipe' => 'aset_tetap'],
            ['nama' => 'Kendaraan', 'tipe' => 'aset_tetap'],
            ['nama' => 'ATK', 'tipe' => 'habis_pakai'],
            ['nama' => 'Alat Olahraga', 'tipe' => 'aset_tetap'],
        ]);

        \App\Models\Ruangan::insert([
            ['nama' => 'Ruang 7A', 'penanggung_jawab' => 'Wali Kelas 7A'],
            ['nama' => 'Ruang 7B', 'penanggung_jawab' => 'Wali Kelas 7B'],
            ['nama' => 'Lab Komputer', 'penanggung_jawab' => 'Kepala Lab Kom'],
            ['nama' => 'Lab IPA', 'penanggung_jawab' => 'Kepala Lab IPA'],
            ['nama' => 'Ruang Guru', 'penanggung_jawab' => 'Waka Kurikulum'],
            ['nama' => 'Perpustakaan', 'penanggung_jawab' => 'Kepala Perpustakaan'],
        ]);

        \App\Models\SumberDana::insert([
            ['nama' => 'Dana BOS'],
            ['nama' => 'Dana BOP'],
            ['nama' => 'Komite Sekolah'],
            ['nama' => 'Hibah Pemerintah'],
            ['nama' => 'Yayasan'],
        ]);
    }
}
