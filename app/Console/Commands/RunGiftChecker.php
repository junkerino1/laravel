<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GiftController;

class RunGiftChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'giftcheck:every-minute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check gift condition and issue gifts.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $giftController = new GiftController();
        $giftController->cronCheckGift();
        info('checkGift function called.');
    }
}
