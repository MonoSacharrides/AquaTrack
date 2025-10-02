<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('billing_month');
            $table->date('reading_date');
            $table->decimal('reading', 10, 2);
            $table->decimal('consumption', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            $table->index(['user_id', 'reading_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('meter_readings');
    }
};
