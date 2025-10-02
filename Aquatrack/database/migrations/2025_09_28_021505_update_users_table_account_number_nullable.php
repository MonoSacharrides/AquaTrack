<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableAccountNumberNullable extends Migration
{
    public function up()
    {
        // Remove the current unique index
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['account_number']);
        });

        // Make the column properly nullable and re-add unique index
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_number')->nullable()->change();
            $table->unique(['account_number']);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['account_number']);
            $table->string('account_number')->nullable(false)->change();
            $table->unique(['account_number']);
        });
    }
}
