<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearReportPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photos:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all report photos';

    /**
     * Execute the console command.
     */
   public function handle()
    {
        Storage::disk('public')->deleteDirectory('report-photos');
        Storage::disk('public')->makeDirectory('report-photos');
        $this->info('All report photos have been cleared!');
    }
}
