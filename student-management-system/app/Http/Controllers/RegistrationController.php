<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function index() { $registrations = CourseRegistration::with('student','course')->latest()->paginate(15); return view('registrations.index',compact('registrations')); }
    public function store(Request $request, Course $course)
    {
        $student = $request->user()->studentProfile;
        abort_unless($student, 403, 'Your account is not linked to a student profile. Contact an administrator.');
        if ($student->registrations()->where('course_id',$course->id)->exists()) throw ValidationException::withMessages(['course' => 'You are already registered for this course.']);
        if ($course->registrations()->count() >= $course->capacity) throw ValidationException::withMessages(['course' => 'This course has reached its registration capacity.']);
        $student->registrations()->create(['course_id'=>$course->id,'registered_at'=>now()->toDateString(),'status'=>'Registered']);
        return back()->with('success','You have successfully registered for '.$course->title.'.');
    }
    public function myCourses(Request $request)
    {
        $user = $request->user();
        if ($user->hasRole('Student')) {
            abort_unless($user->studentProfile,403,'Your account is not linked to a student profile.');
            $registrations = $user->studentProfile->registrations()->with('course.lecturer')->latest()->paginate(15);
            return view('registrations.mine',compact('registrations'));
        }
        if ($user->hasRole('Lecturer')) {
            $courseIds = $user->taughtCourses()->pluck('id');
            $registrations = CourseRegistration::with('student','course')->whereIn('course_id',$courseIds)->latest()->paginate(15);
            return view('registrations.index',compact('registrations'));
        }
        return redirect()->route('registrations.index');
    }
    public function destroy(CourseRegistration $registration) { $registration->delete(); return back()->with('success','Registration removed.'); }
}
