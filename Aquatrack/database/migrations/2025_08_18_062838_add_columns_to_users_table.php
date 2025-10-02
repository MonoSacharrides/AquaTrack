<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('lastname')->after('name')->nullable();
            $table->string('zone')->after('phone')->nullable();
            $table->string('barangay')->after('zone')->nullable();
            $table->string('municipality')->after('barangay')->default('Clarin');
            $table->string('province')->after('municipality')->default('Bohol');
            $table->date('date_installed')->after('province')->nullable();
            $table->string('brand')->after('date_installed')->nullable();
            $table->string('serial_number')->after('brand')->unique()->nullable();
            $table->string('size')->after('serial_number')->nullable();
            $table->boolean('enabled')->default(true)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'lastname',
                'zone',
                'barangay',
                'municipality',
                'province',
                'date_installed',
                'brand',
                'serial_number',
                'size',
                'enabled'
            ]);
        });
    }
};
