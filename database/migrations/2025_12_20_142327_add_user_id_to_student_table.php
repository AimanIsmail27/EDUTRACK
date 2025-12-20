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
    Schema::table('student', function (Blueprint $table) {
        // Add user_id column after the Primary Key (MatricID)
        // We use unsignedBigInteger because it must match the 'id' type in 'users' table
        $table->unsignedBigInteger('user_id')->nullable()->after('MatricID');

        // Optional: Add a foreign key constraint for data integrity
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('student', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
};
