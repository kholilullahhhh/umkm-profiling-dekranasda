<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    // protected $table = 'settings';
    protected $fillable = [
        'id',
        'logo',
        'name',
        'description',
        'address',
        'email',
        'phone_number',
        'whatsapp',
        'facebook',
        'instagram',
        'active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = ['deleted_at'];
}
