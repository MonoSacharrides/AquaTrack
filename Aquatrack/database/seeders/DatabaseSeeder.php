<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Storage::disk('public')->deleteDirectory('report-photos');
        Storage::disk('public')->makeDirectory('report-photos');

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
    }
}
