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
        Schema::create('courses', function (Blueprint $table) {

            // PRIMARY KEY
            $table->string('C_Code')->primary();

            // COURSE DETAILS
            $table->string('C_Name');
            $table->integer('C_Hour');

            /*
             * Prerequisites
             * Stored as JSON or comma-separated values
             * Example: ["BCN1010", "BCN1005"]
             */
            $table->json('C_Prerequisites')->nullable();

            /*
             * Semester offered:
             * 1, 2, or 3
             */
            $table->tinyInteger('C_SemOffered');

            // Instructor name
            $table->string('C_Instructor')->nullable();

            // Long description
            $table->text('C_Description')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
