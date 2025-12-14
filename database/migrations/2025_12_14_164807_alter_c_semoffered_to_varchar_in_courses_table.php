<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Change the column type from tinyint to VARCHAR
            $table->string('C_SemOffered', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Revert the column type back to tinyint
            $table->tinyInteger('C_SemOffered')->nullable()->change();
        });
    }
};
