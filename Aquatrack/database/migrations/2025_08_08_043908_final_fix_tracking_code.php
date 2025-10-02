<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        // 1. Add column if it doesn't exist (with temporary nullable state)
        if (!Schema::hasColumn('reports', 'tracking_code')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->string('tracking_code')->nullable()->after('id');
            });
        }

        // 2. Fill empty tracking codes
        DB::table('reports')
            ->whereNull('tracking_code')
            ->orWhere('tracking_code', '')
            ->orderBy('id')
            ->each(function ($report) {
                $trackingCode = 'AQT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
                DB::table('reports')
                    ->where('id', $report->id)
                    ->update(['tracking_code' => $trackingCode]);
            });

        // 3. Modify column to be not nullable and unique
        Schema::table('reports', function (Blueprint $table) {
            $table->string('tracking_code')
                ->nullable(false)
                ->unique()
                ->change();
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropUnique('reports_tracking_code_unique');
            $table->dropColumn('tracking_code');
        });
    }
};
