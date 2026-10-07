<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $guarded = [];

    public function unitBarang()
    {
        return $this->belongsTo(UnitBarang::class);
    }
}
