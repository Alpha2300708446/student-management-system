<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $stats = [
            'students' => Student::count(),
            'courses' => Course::count(),
            'registrations' => CourseRegistration::count(),
            'lecturers' => \App\Models\User::role('Lecturer')->count(),
        ];
        $recentStudents = $user->hasRole('Administrator') ? Student::latest()->take(5)->get() : collect();
        $availableCourses = Course::with('lecturer')->latest()->take(6)->get();
        $myRegistrations = collect();
        if ($user->hasRole('Student') && $user->studentProfile) {
            $myRegistrations = $user->studentProfile->registrations()->with('course')->latest()->take(5)->get();
        }
        return view('dashboard.index', compact('stats', 'recentStudents', 'availableCourses', 'myRegistrations'));
    }
}
