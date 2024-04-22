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
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('evaluated_id')->constrained('users')->where('role', 'Teacher');
            $table->string('evaluated_first_name');
            $table->string('evaluated_last_name');
            $table->string('subject_name');
            $table->string('semester');
            $table->string('comment');
            $table->string('suggestion');
            $table->string('strand');
            $table->string('year_level');
            $table->string('approve_status')->default('Pending');
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
