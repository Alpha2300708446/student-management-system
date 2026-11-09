<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::with('lecturer')->when($request->search, fn($q,$s) => $q->where('title','like',"%{$s}%")->orWhere('course_code','like',"%{$s}%"))->latest()->paginate(10)->withQueryString();
        return view('courses.index', compact('courses'));
    }
    public function create() { return view('courses.create', ['course' => new Course(), 'lecturers' => User::role('Lecturer')->orderBy('name')->get()]); }
    public function store(Request $request) { Course::create($this->validated($request)); return redirect()->route('courses.index')->with('success','Course created successfully.'); }
    public function show(Course $course) { $course->load('lecturer','students'); return view('courses.show', compact('course')); }
    public function edit(Course $course) { return view('courses.edit', ['course' => $course, 'lecturers' => User::role('Lecturer')->orderBy('name')->get()]); }
    public function update(Request $request, Course $course) { $course->update($this->validated($request,$course)); return redirect()->route('courses.show',$course)->with('success','Course updated successfully.'); }
    public function destroy(Course $course) { $course->delete(); return redirect()->route('courses.index')->with('success','Course deleted successfully.'); }
    public function assigned(Request $request) { $courses = Course::with('students')->where('lecturer_id',$request->user()->id)->paginate(10); return view('courses.assigned',compact('courses')); }
    private function validated(Request $request, ?Course $course = null): array
    {
        return $request->validate([
            'course_code' => ['required','string','max:30',Rule::unique('courses','course_code')->ignore($course?->id)],
            'title' => ['required','string','max:150'], 'description' => ['nullable','string','max:2000'],
            'credit_units' => ['required','integer','min:1','max:20'], 'semester' => ['required',Rule::in(['Semester I','Semester II','Recess'])],
            'academic_year' => ['required','string','max:20'], 'lecturer_id' => ['nullable','exists:users,id'], 'capacity' => ['required','integer','min:1','max:1000'],
        ]);
    }
}
