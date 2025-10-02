<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dismissed_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('notification_id'); // The virtual notification ID like 'new_report_123'
            $table->string('type'); // notification type
            $table->timestamps();

            $table->unique(['user_id', 'notification_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('dismissed_notifications');
    }
};
