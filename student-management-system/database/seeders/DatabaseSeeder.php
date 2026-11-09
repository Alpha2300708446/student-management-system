<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        foreach (['Administrator','Lecturer','Student'] as $role) Role::firstOrCreate(['name'=>$role,'guard_name'=>'web']);
        foreach (['manage students','manage courses','manage registrations','view assigned courses','view own registrations','register courses'] as $permission) Permission::firstOrCreate(['name'=>$permission,'guard_name'=>'web']);
        Role::findByName('Administrator')->syncPermissions(Permission::all());
        Role::findByName('Lecturer')->syncPermissions(['view assigned courses']);
        Role::findByName('Student')->syncPermissions(['view own registrations','register courses']);

        $admin = User::firstOrCreate(['email'=>'admin@campusconnect.test'],['name'=>'System Administrator','password'=>Hash::make('Admin@12345')]); $admin->syncRoles(['Administrator']);
        $lecturer = User::firstOrCreate(['email'=>'lecturer@campusconnect.test'],['name'=>'Grace Nakato','password'=>Hash::make('Lecturer@123')]); $lecturer->syncRoles(['Lecturer']);
        $students = [
            ['STU001','Amina','Nabirye','amina@campusconnect.test'], ['STU002','Brian','Okello','brian@campusconnect.test'],
            ['STU003','Clara','Nansubuga','clara@campusconnect.test'], ['STU004','Daniel','Mugisha','daniel@campusconnect.test'],
            ['STU005','Esther','Atim','esther@campusconnect.test'],
        ];
        foreach ($students as [$number,$first,$last,$email]) {
            $user = User::firstOrCreate(['email'=>$email],['name'=>"$first $last",'password'=>Hash::make('Student@123')]); $user->syncRoles(['Student']);
            Student::firstOrCreate(['student_number'=>$number],['user_id'=>$user->id,'first_name'=>$first,'last_name'=>$last,'email'=>$email,'phone'=>'0700000000','gender'=>in_array($first,['Amina','Clara','Esther'])?'Female':'Male','date_of_birth'=>'2003-05-15','programme'=>'Bachelor of Business Computing','year_of_study'=>3,'status'=>'Active']);
        }
        $courseData = [
            ['BUC3101','Advanced Web Application Development',4,'Semester I'], ['BUC3102','Database Systems',3,'Semester I'],
            ['BUC3103','Systems Analysis and Design',3,'Semester II'], ['BUC3104','Computer Networks',3,'Semester II'],
        ];
        foreach ($courseData as [$code,$title,$units,$semester]) Course::firstOrCreate(['course_code'=>$code],['title'=>$title,'description'=>'Sample course record for demonstration.','credit_units'=>$units,'semester'=>$semester,'academic_year'=>'2026/2027','lecturer_id'=>$lecturer->id,'capacity'=>100]);
        $firstStudent = Student::where('student_number','STU001')->first();
        foreach (Course::take(2)->get() as $course) CourseRegistration::firstOrCreate(['student_id'=>$firstStudent->id,'course_id'=>$course->id],['registered_at'=>now()->toDateString(),'status'=>'Registered']);
    }
}
