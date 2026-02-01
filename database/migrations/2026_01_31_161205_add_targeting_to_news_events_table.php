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
        Schema::table('news_events', function (Blueprint $table) {
            $table->string('target_type')->default('all')->after('type'); // all, batch, course, batch_course
            $table->string('target_batch')->nullable()->after('target_type');
            $table->foreignId('target_course_id')->nullable()->after('target_batch')->constrained('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_events', function (Blueprint $table) {
            $table->dropForeign(['target_course_id']);
            $table->dropColumn(['target_type', 'target_batch', 'target_course_id']);
        });
    }
};
