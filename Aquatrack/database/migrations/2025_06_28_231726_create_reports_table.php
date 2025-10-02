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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
             $table->string('reporter_name')->nullable();
            $table->string('municipality');
            $table->string('province');
            $table->string('barangay');
            $table->string('purok');
            $table->text('description');

            // Remove the 'after' clauses for create table

            $table->string('reporter_phone')->nullable();

            $table->timestamps();
        });

        Schema::create('report_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('type')->default('photo'); // 'photo' or 'video'
            $table->unsignedInteger('size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_photos');
        Schema::dropIfExists('reports');
    }
};
