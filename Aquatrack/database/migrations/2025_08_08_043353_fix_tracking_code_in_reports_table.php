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
        // First check if the column exists
        if (Schema::hasColumn('reports', 'tracking_code')) {
            // 1. Make sure all records have a tracking code
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

            // 2. Add unique constraint to existing column
            Schema::table('reports', function (Blueprint $table) {
                $table->string('tracking_code')
                    ->nullable(false)
                    ->unique()
                    ->change();
            });
        }
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasIndex('reports', 'reports_tracking_code_unique')) {
                $table->dropIndex('reports_tracking_code_unique');
            }
        });
    }
};
