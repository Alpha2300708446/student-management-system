<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['course_code', 'title', 'description', 'credit_units', 'semester', 'academic_year', 'lecturer_id', 'capacity'];
    public function lecturer() { return $this->belongsTo(User::class, 'lecturer_id'); }
    public function registrations() { return $this->hasMany(CourseRegistration::class); }
    public function students() { return $this->belongsToMany(Student::class, 'course_registrations')->withPivot('registered_at', 'status')->withTimestamps(); }
}
