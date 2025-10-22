<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_usaha_id',
        'nama_usaha',
        'pemilik',
        'alamat',
        'kabupaten',
        'tahun_berdiri',
        'skala_usaha',
        'omset_per_tahun',
        'kontak',
        'status_binaan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisUsaha()
    {
        return $this->belongsTo(JenisUsaha::class);
    }

    public function pembinaan()
    {
        return $this->hasMany(Pembinaan::class);
    }

    public function profiling()
    {
        return $this->hasOne(Profiling::class);
    }
}
