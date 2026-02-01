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
        Schema::table('evaluation_forms', function (Blueprint $table) {
            $table->integer('version')->default(1)->after('type');
            $table->foreignId('parent_form_id')->nullable()->after('version')->constrained('evaluation_forms')->nullOnDelete();
            $table->boolean('is_draft')->default(true)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluation_forms', function (Blueprint $table) {
            $table->dropForeign(['parent_form_id']);
            $table->dropColumn(['version', 'parent_form_id', 'is_draft']);
        });
    }
};
