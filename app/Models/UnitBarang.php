<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitBarang extends Model
{
    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function sumberDana()
    {
        return $this->belongsTo(SumberDana::class);
    }

    public function mutasiBarangs()
    {
        return $this->hasMany(MutasiBarang::class)->orderBy('tanggal_mutasi', 'desc');
    }
}
