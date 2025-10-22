<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profiling extends Model
{
    protected $fillable = [
        'umkm_id',
        'tenaga_kerja',
        'kapasitas_produksi',
        'bahan_baku',
        'pasar',
        'kebutuhan_pembinaan',
        'potensi_pengembangan'
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}
