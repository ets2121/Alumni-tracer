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
        $tables = ['users', 'alumni_profiles', 'news_events', 'gallery_albums', 'ched_memos', 'evaluation_forms', 'chat_groups', 'activity_logs', 'courses'];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'department_name')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->index('department_name');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['users', 'alumni_profiles', 'news_events', 'gallery_albums', 'ched_memos', 'evaluation_forms', 'chat_groups', 'activity_logs', 'courses'];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'department_name')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['department_name']);
                });
            }
        }
    }
};
