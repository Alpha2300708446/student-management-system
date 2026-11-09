<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::query()->when($request->search, function ($q, $search) { $q->where(fn($sub) => $sub->where('first_name','like',"%{$search}%")->orWhere('last_name','like',"%{$search}%")->orWhere('student_number','like',"%{$search}%")->orWhere('email','like',"%{$search}%")); })->latest()->paginate(10)->withQueryString();
        return view('students.index', compact('students'));
    }
    public function create() { return view('students.create', ['student' => new Student()]); }
    public function store(Request $request)
    {
        $data = $this->validated($request);
        Student::create($data);
        return redirect()->route('students.index')->with('success', 'Student record created successfully.');
    }
    public function show(Student $student) { $student->load('registrations.course'); return view('students.show', compact('student')); }
    public function edit(Student $student) { return view('students.edit', compact('student')); }
    public function update(Request $request, Student $student)
    {
        $student->update($this->validated($request, $student));
        return redirect()->route('students.show', $student)->with('success', 'Student record updated successfully.');
    }
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student record deleted.');
    }
    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'student_number' => ['required','string','max:40',Rule::unique('students','student_number')->ignore($student?->id)],
            'first_name' => ['required','string','max:100'], 'last_name' => ['required','string','max:100'],
            'email' => ['required','email','max:150',Rule::unique('students','email')->ignore($student?->id)],
            'phone' => ['nullable','string','max:30'], 'gender' => ['required',Rule::in(['Female','Male','Other'])],
            'date_of_birth' => ['nullable','date','before:today'], 'programme' => ['required','string','max:150'],
            'year_of_study' => ['required','integer','min:1','max:6'], 'status' => ['required',Rule::in(['Active','Deferred','Graduated'])],
        ]);
    }
}
