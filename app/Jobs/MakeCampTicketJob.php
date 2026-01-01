<?php

namespace App\Jobs;

use App\Models\CampTicket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Throwable;
class MakeCampTicketJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public CampTicket $ticket)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->ticket->update(['status' => 1]);
        $request = $this->ticket->request;
        $conf = config('services.camp');
        $req = Http::baseUrl($conf['base_url'])
            ->acceptJson()
            ->withHeader('X-API-KEY' , $conf['api_key'])
            ->post('/api/v1/camp-tickets' , [
                'code' => $request->plan_data['camp_code'],
                'max_capacity_allocated' => $request->students,
                'national_id' => $request->user->national_id,
                'request_id' => $request->id,
                'amount' => $request->final_amount ?? $request->offer_amount ?? $request->amount ?? 0,
                'arman_id' =>  $request->user->id,
            ]);
        if ($req->successful()) {
            $data = $req->json('data');
            $request->fill(['camp_ticket_id' => $data['id']])->save();
            $this->ticket->update(['status' => 2, 'result' => $data]);
            return;
        }
        $this->ticket->update(['status' => 0, 'result' => $req->json()]);
    }

    public function failed(?Throwable $exception)
    {
        $this->ticket->update(['status' => 0 , 'result' => $exception]);
    }
}
