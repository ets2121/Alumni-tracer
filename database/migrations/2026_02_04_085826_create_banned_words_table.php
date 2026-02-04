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
        Schema::create('banned_words', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('department_name')->nullable()->index();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint: A word can catch duplicates within the same scope (Global or Dept)
            // But we can allow 'fool' in Global and 'fool' in Dept (redundant but safe)
            // Better to enforce unique pair
            $table->unique(['word', 'department_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_words');
    }
};
