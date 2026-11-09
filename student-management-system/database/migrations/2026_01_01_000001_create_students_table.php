<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('students', function (Blueprint $table) {
        $table->id(); $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
        $table->string('student_number')->unique(); $table->string('first_name'); $table->string('last_name');
        $table->string('email')->unique(); $table->string('phone')->nullable(); $table->enum('gender',['Female','Male','Other']);
        $table->date('date_of_birth')->nullable(); $table->string('programme'); $table->unsignedTinyInteger('year_of_study');
        $table->enum('status',['Active','Deferred','Graduated'])->default('Active'); $table->timestamps();
    }); }
    public function down(): void { Schema::dropIfExists('students'); }
};
