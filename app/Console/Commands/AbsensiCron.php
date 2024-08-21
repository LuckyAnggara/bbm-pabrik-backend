<?php

namespace App\Console\Commands;

use App\Http\Controllers\AbsensiController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AbsensiCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Narik Data Absensi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Sample
        $resullt = AbsensiController::fetchPagi(Carbon::now());
        if ($resullt) {
            Log::info($resullt);
        } else {
            Log::info('gagal');
        }
    }
}
