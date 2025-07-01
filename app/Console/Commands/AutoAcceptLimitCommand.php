<?php

namespace App\Console\Commands;

use App\Enums\RequestStep;
use App\Models\Report;
use App\Models\Request;
use Illuminate\Console\Command;

class AutoAcceptLimitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-accept-limit {--target=requests}';

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
                    ->whereNotNull('auto_accept_at')
                    ->where('auto_accept_at','<=',now())
                    ->where('step' , RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER)
                    ->take(10)
                    ->get();
                foreach ($reports as $report) {
                    $report->toNextStep()->save();
                }
                break;
            default:
                $requests = Request::query()
                    ->whereNotNull('auto_accept_at')
                    ->where('auto_accept_at','<=',now())
                    ->where('step' , RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER)
                    ->take(10)
                    ->get();
                foreach ($requests as $request) {
                    $request->toNextStep()->save();
                }
        }
    }
}
