<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'student_id',
        'qr_code',
        'latitude',
        'longitude',
        'is_aktif',
        'pesan',
        'attendance_time'
    ];

    protected $casts = [
        'attendance_time' => 'datetime',
        'is_valid' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}