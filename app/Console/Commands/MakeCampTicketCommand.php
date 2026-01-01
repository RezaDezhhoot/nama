<?php

namespace App\Console\Commands;

use App\Jobs\MakeCampTicketJob;
use App\Models\CampTicket;
use Illuminate\Console\Command;

class MakeCampTicketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-camp-ticket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = CampTicket::query()
            ->with(['request','request.user'])
            ->latest()
            ->where('status' , 0)
            ->take(50)
            ->chunkById(10 , function ($items) {
                foreach ($items as $item) {
                    dispatch(new MakeCampTicketJob($item));
                }
            });
    }
}
