<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProductionUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Production Status from New Order to Work in Process';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
    }
}
