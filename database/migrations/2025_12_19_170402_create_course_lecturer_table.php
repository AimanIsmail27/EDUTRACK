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
    Schema::create('course_lecturer', function (Blueprint $table) {
        $table->id();
        $table->string('course_code');
        $table->unsignedBigInteger('user_id'); // The Involved Lecturer's ID
        
        // Links
        $table->foreign('course_code')->references('C_Code')->on('courses')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lecturer');
    }
};
