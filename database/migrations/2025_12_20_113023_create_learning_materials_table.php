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
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            
            // Link to the course (Foreign Key)
            $table->string('course_code');
            
            // Link to the lecturer who uploaded it
            $table->unsignedBigInteger('user_id');

            // For your Weekly Accordion sorting (1-14)
            $table->integer('week_number');

            // The title and category for the "Better Sorting" you mentioned
            $table->string('title'); // e.g., "Introduction to Laravel"
            $table->string('category'); // e.g., "Notes", "Lab Sheet", "Slides"

            // File storage information
            $table->string('file_path'); // Path in the storage folder
            $table->string('file_original_name'); // Original name of the uploaded file
            $table->string('file_extension'); // pdf, docx, etc.
            
            $table->timestamps();

            // Relationships
            // onDelete('cascade') means if a course is deleted, the materials are too.
            $table->foreign('course_code')->references('C_Code')->on('courses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
    }
};