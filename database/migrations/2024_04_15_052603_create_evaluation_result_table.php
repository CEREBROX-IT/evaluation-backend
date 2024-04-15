<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluation_result', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluation');
            $table->foreignId('question_id')->constrained('question');
            $table->string('evaluation_for');
            $table->string('question_type');
            $table->string('question_description');
            $table->string('rating');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_result');
    }
};
