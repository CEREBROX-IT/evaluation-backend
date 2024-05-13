<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('session');
            $table->string('school_year', 30);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('evaluated_id')->constrained('users')->where('role', 'Teacher');
            $table->string('evaluated_full_name', 80);
            $table->string('class_size', 15);
            $table->string('no_of_student_present', 15);
            $table->string('no_of_student_late', 15);
            $table->string('gender', 10);
            $table->string('category', 20);
            $table->string('office_services', 30);
            $table->string('length_of_service', 50);
            $table->string('subject_name', 50);
            $table->string('semester', 50);
            $table->string('comment', 275);
            $table->string('suggestion', 275);
            $table->string('strand', 50);
            $table->string('year_level', 10);
            $table->string('approve_status')->default('Pending', 10);
            $table->tinyInteger('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation');
    }
};
