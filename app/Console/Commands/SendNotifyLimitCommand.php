<?php

namespace App\Console\Commands;

use App\Enums\RequestStep;
use App\Models\Report;
use App\Models\Request;
use Illuminate\Console\Command;

class SendNotifyLimitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notify-limit {--target=requests}';

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
        switch ($this->option('target')) {
            case "reports":
                $reports = Report::query()
                    ->whereNotNull(['next_notify_at','notify_period'])
                    ->where('next_notify_at','<=',now())
                    ->where('step' , RequestStep::APPROVAL_AREA_INTERFACE)
                    ->take(3)
                    ->get();
                foreach ($reports as $report) {
//                    $report->update([
//                        'next_notify_at' => now()->addHours($report->notify_period)
//                    ]);
                }
                break;
            default:
                $requests = Request::query()
                    ->whereNotNull(['next_notify_at','notify_period'])
                    ->where('next_notify_at','<=',now())
                    ->where('step' , RequestStep::APPROVAL_AREA_INTERFACE)
                    ->take(3)
                    ->get();
                foreach ($requests as $request) {
//                    $request->update([
//                        'next_notify_at' => now()->addHours($request->notify_period)
//                    ]);
                }
        }
    }
}
