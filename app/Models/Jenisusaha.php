<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisUsaha extends Model
{
    protected $table = 'jenis_usaha';
    protected $fillable = ['nama_jenis', 'deskripsi'];

    public function umkms()
    {
        return $this->hasMany(Umkm::class);
    }
}

