<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function unitBarang()
    {
        return $this->belongsTo(UnitBarang::class);
    }

    public function ruanganAsal()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_asal_id');
    }

    public function ruanganTujuan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_tujuan_id');
    }
}
