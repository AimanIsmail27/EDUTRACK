<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('course_student', function (Blueprint $table) {
        $table->id();
        $table->string('course_code'); 
        $table->string('student_matric'); 
        $table->integer('semester');
        $table->year('year');
        $table->timestamps();

        // Foreign keys ensure data integrity
        $table->foreign('course_code')->references('C_Code')->on('courses')->onDelete('cascade');
        $table->foreign('student_matric')->references('MatricID')->on('student')->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_student');
    }
};
