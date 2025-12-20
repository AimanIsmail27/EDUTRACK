<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->string('course_code');
            $table->foreign('course_code')->references('C_Code')->on('courses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('due_at')->nullable();
            $table->unsignedInteger('total_marks')->default(100);
            $table->enum('status', ['Draft', 'Published', 'Scheduled'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
