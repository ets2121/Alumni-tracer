<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('news_events', function (Blueprint $blueprint) {
            // Visibility and RBAC
            if (!Schema::hasColumn('news_events', 'visibility_type')) {
                $blueprint->enum('visibility_type', ['all', 'department'])->default('all')->after('type');
            }

            // Note: department_name exists, but we'll ensure it's clear for isolation

            // Post Type extension
            // We can't easily change enum in migration for some DBs, adding post_category or similar if needed
            // But 'type' already has 'news', 'event', 'announcement'. We'll use 'job' as well.
            // Using raw SQL to update enum if needed, or just handle it in app logic if column is string.
            // Let's check 'type' column type.
        });

        // Job specific fields
        Schema::table('news_events', function (Blueprint $blueprint) {
            $blueprint->string('job_company')->nullable()->after('location');
            $blueprint->string('job_salary')->nullable()->after('job_company');
            $blueprint->string('job_link')->nullable()->after('job_salary');
            $blueprint->timestamp('job_deadline')->nullable()->after('job_link');
        });
    }

    public function down(): void
    {
        Schema::table('news_events', function (Blueprint $blueprint) {
            $blueprint->dropColumn([
                'visibility_type',
                'job_company',
                'job_salary',
                'job_link',
                'job_deadline'
            ]);
        });
    }
};
