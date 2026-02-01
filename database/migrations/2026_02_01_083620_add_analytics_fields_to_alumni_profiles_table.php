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
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->string('field_of_work')->nullable()->after('employment_status');
            $table->string('work_status')->nullable()->after('field_of_work'); // Permanent, Contractual, Job Order
            $table->string('establishment_type')->nullable()->after('work_status'); // Public, Private
            $table->string('work_location')->nullable()->after('establishment_type'); // Local, Overseas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->dropColumn(['field_of_work', 'work_status', 'establishment_type', 'work_location']);
        });
    }
};
