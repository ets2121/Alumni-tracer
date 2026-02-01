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
            $table->string('slug')->after('title')->nullable();
            $table->string('author')->nullable()->after('content');
            $table->json('category')->nullable()->after('author'); // Storing tags as JSON array
            $table->string('registration_link')->nullable()->after('location');
            $table->boolean('is_pinned')->default(false)->after('registration_link');
            $table->dateTime('expires_at')->nullable()->after('is_pinned');
        });

        // Modify the enum column to include 'announcement'
        // Using raw SQL for MySQL compatibility as Schema builder doesn't support modifying enums easily
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE news_events MODIFY COLUMN type ENUM('news', 'event', 'announcement') NOT NULL");
        } catch (\Exception $e) {
            // Fallback for SQLite or other drivers: just change to string if enum modification fails
            // Or ignore if strict mode prevents it. For now assuming MySQL.
        }

        Schema::create('news_event_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_event_id')->constrained('news_events')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_event_photos');

        Schema::table('news_events', function (Blueprint $table) {
            $table->dropColumn(['slug', 'author', 'category', 'registration_link', 'is_pinned', 'expires_at']);
        });

        // Reverting enum is optional/tricky, leaving as is or could revert to just news/event
    }
};
