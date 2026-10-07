<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';
    protected $guarded = [];

    public function unitBarang()
    {
        return $this->belongsTo(UnitBarang::class);
    }
}
