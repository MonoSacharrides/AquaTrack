<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::table('announcements', function (Blueprint $table) {
            // Only add if it doesn't exist
            if (!Schema::hasColumn('announcements', 'target_audience')) {
                $table->enum('target_audience', ['all', 'admin', 'staff', 'customer'])->default('all');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'target_audience')) {
                $table->dropColumn('target_audience');
            }
        });
    }
};
