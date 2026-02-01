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
        Schema::table('ched_memos', function (Blueprint $table) {
            $table->text('description')->nullable()->after('date_issued');
            $table->string('category')->default('Institutional policies')->after('memo_number');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ched_memos', function (Blueprint $table) {
            $table->dropColumn(['description', 'category']);
        });
    }
};
