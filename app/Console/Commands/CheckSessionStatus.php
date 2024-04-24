<?php

namespace App\Console\Commands;

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckSessionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-session:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check session status and update if end date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get sessions where end_date is less than today
        $sessions = Session::where('end_date', '<', Carbon::now())->get();

        // Update status to 2 for fetched sessions
        foreach ($sessions as $session) {
            $session->status = \App\Models\Session::COMPLETED;
            $session->save();
        }

        $this->info('Session status updated successfully.');
    }
}
