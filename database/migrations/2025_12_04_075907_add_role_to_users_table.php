<?php

// database/migrations/XXXX_XX_XX_XXXXXX_add_role_to_users_table.php

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
        Schema::table('users', function (Blueprint $table) {
            // Add the 'role' column. We'll use string to store role names.
            // You can change 'lecturer' to 'staff' if you prefer.
            $table->string('role')->after('password')->default('student'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the 'role' column when rolling back
            $table->dropColumn('role');
        });
    }
};
