<?php

namespace App\Console\Commands;

use App\Http\Controllers\manager\AccessoriesBalanceController;
use Illuminate\Console\Command;

class GenerateAccessoriesBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accessories:balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        app(AccessoriesBalanceController::class)
            ->calculateAndSaveBalance();

        $this->info('Accessories balance berhasil digenerate');
    }
}
