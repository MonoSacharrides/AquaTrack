<?php

namespace Database\Seeders;

use App\Models\MeterReading;
use Illuminate\Database\Seeder;

class UpdateMeterReadingsStatusSeeder extends Seeder
{
    public function run()
    {
        MeterReading::whereNull('status')->update(['status' => 'Pending']);
    }
}
