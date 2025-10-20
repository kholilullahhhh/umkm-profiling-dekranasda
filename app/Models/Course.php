<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'lecturer_id',
        'allowed_latitude',
        'allowed_longitude',
        'allowed_radius',
        'start_time',
        'end_time',
        'is_active'
    ];

    protected $casts = [
        'allowed_latitude' => 'decimal:8',
        'allowed_longitude' => 'decimal:8',
        'is_active' => 'boolean'
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}