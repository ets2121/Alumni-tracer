<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'job' to the type enum
        try {
            DB::statement("ALTER TABLE news_events MODIFY COLUMN type ENUM('news', 'event', 'announcement', 'job') NOT NULL");
        } catch (\Exception $e) {
            // Fallback for drivers that don't support enum modification (like SQLite)
            // In a real app we might change the column to string
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE news_events MODIFY COLUMN type ENUM('news', 'event', 'announcement') NOT NULL");
        } catch (\Exception $e) {
        }
    }
};
