<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckReservationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-reservation:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check reservation status and update if end date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get reservations where end_date is less than today
        $reservations = Reservation::where('end_date', '<', Carbon::now())->get();

        // Update status to completed for fetched reservations
        foreach ($reservations as $reservation) {
            $reservation->status = \App\Models\Reservation::COMPLETED;
            $reservation->save();
        }

        $this->info('Reservations status updated successfully.');
    }
}
