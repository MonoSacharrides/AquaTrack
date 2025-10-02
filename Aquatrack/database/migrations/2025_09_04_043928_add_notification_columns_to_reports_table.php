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
        Schema::table('reports', function (Blueprint $table) {
            $table->boolean('viewed_by_admin')->default(false);
            $table->boolean('viewed_by_staff')->default(false);
            $table->boolean('viewed_by_user')->default(true);
            $table->boolean('update_viewed_by_admin')->default(false);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'viewed_by_admin',
                'viewed_by_staff',
                'viewed_by_user',
                'update_viewed_by_admin',
                'assigned_to'
            ]);
        });
    }
};
