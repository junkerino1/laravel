<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoveToProcessingCommand extends Command
{
    protected $signature = 'move:to-processing';
    protected $description = 'Call the move_to_processing stored procedure';

   public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::statement('CALL process_receipt();');
        $this->info('move_to_processing procedure called.');
    }
}
