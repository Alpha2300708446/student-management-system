<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'student_number', 'first_name', 'last_name', 'email', 'phone', 'gender', 'date_of_birth', 'programme', 'year_of_study', 'status'];
    protected $casts = ['date_of_birth' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function registrations() { return $this->hasMany(CourseRegistration::class); }
    public function courses() { return $this->belongsToMany(Course::class, 'course_registrations')->withPivot('registered_at', 'status')->withTimestamps(); }
    public function getFullNameAttribute(): string { return trim($this->first_name.' '.$this->last_name); }
}
