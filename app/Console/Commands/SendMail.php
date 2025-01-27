<?php

namespace App\Console\Commands;

use App\Http\Controllers\MailController;
use Illuminate\Console\Command;

class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendmail:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email automatically daily';

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
        $mailController = new MailController();
        $mailController->sendMail();
        info('sendMail function called.');
        return 0;
    }
}
