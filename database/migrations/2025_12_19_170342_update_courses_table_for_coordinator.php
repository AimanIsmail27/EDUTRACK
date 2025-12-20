<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // 1. Remove the old text-based instructor column
            if (Schema::hasColumn('courses', 'C_Instructor')) {
                $table->dropColumn('C_Instructor');
            }

            // 2. Add the new coordinator_id linked to the users table
            // We make it nullable in case a course is created before a lecturer is assigned
            $table->unsignedBigInteger('coordinator_id')->after('C_SemOffered')->nullable();
            
            // 3. Set up the foreign key relationship
            $table->foreign('coordinator_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // If a lecturer user is deleted, the course stays but coordinator becomes null
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Reverse the changes if we roll back
            $table->dropForeign(['coordinator_id']);
            $table->dropColumn('coordinator_id');
            $table->string('C_Instructor')->nullable();
        });
    }
};