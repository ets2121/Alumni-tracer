<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \DB::statement("UPDATE users 
                       INNER JOIN alumni_profiles ON users.id = alumni_profiles.user_id 
                       SET users.department_name = alumni_profiles.department_name 
                       WHERE users.role = 'alumni' AND alumni_profiles.department_name IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
