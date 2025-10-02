<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // YYYY_MM_DD_HHMMSS_format_existing_account_numbers.php
    public function up(): void
    {
        if (Schema::hasColumn('users', 'account_number')) {
            User::whereNotNull('account_number')
                ->chunk(200, function ($users) {
                    foreach ($users as $user) {
                        $clean = str_replace('-', '', $user->account_number);
                        $formatted = substr($clean, 0, 3) . '-' . substr($clean, 3, 2) . '-' . substr($clean, 5, 3);

                        if ($user->account_number !== $formatted) {
                            $user->update(['account_number' => $formatted]);
                        }
                    }
                });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
