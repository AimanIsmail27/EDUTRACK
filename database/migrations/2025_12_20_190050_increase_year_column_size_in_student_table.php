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
        // Change Year column from integer to varchar to support "2023-2024" format
        Schema::table('student', function (Blueprint $table) {
            $table->string('Year', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to integer (note: this may cause data loss if values are not numeric)
        Schema::table('student', function (Blueprint $table) {
            $table->integer('Year')->change();
        });
    }
};
