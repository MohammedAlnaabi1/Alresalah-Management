<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'subject', 'points', 'category', 'semester', 'academic_year', 'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
