<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $guarded = [];

    public function unitBarangs()
    {
        return $this->hasMany(UnitBarang::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
