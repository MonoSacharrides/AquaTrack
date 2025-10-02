<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixAccountNumberUniqueConstraint extends Migration
{
    public function up()
    {
        // For MySQL: Drop the current index and create a new one that allows multiple NULLs
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users DROP INDEX users_account_number_unique;');
            DB::statement('ALTER TABLE users ADD UNIQUE users_account_number_unique (account_number);');
        }
    }

    public function down()
    {
        // Revert back to the original constraint
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users DROP INDEX users_account_number_unique;');
            DB::statement('ALTER TABLE users ADD CONSTRAINT users_account_number_unique UNIQUE (account_number);');
        }
    }
}
