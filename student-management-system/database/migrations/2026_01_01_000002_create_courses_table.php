<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('courses', function (Blueprint $table) {
        $table->id(); $table->string('course_code')->unique(); $table->string('title'); $table->text('description')->nullable();
        $table->unsignedTinyInteger('credit_units')->default(3); $table->enum('semester',['Semester I','Semester II','Recess']);
        $table->string('academic_year'); $table->foreignId('lecturer_id')->nullable()->constrained('users')->nullOnDelete();
        $table->unsignedSmallInteger('capacity')->default(100); $table->timestamps();
    }); }
    public function down(): void { Schema::dropIfExists('courses'); }
};
