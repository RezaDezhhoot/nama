<?php

namespace App\Console\Commands;

use App\Enums\OperatorRole;
use App\Enums\RequestStatus;
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
        $message = "تایید اتوماتیک مسئول فرهنگی";
        switch ($this->option('target')) {
            case "reports":
                $reports = Report::query()
                    ->whereNotNull('auto_accept_at')
                    ->with('controller')
                    ->where('auto_accept_at','<=',now())
                    ->where('step' , RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER)
                    ->take(10)
                    ->get();
                foreach ($reports as $report) {
                    if ($report->controller) {
                        $report->comments()->create([
                            'user_id' => $report->controller->id,
                            'body' => $message,
                            'display_name' => RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER->title(),
                            'from_status' => RequestStatus::IN_PROGRESS,
                            'to_status' => RequestStatus::IN_PROGRESS,
                            'step' => RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER
                        ]);
                    }
                    $report->message = $message;
                    if (! $report->messages) {
                        $report->messages = [];
                    }
                    $messages = $report->messages;
                    $messages[RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER->value] = $message;
                    $report->messages = $messages;
                    $report->toNextStep()->save();
                }
                break;
            default:
                $requests = Request::query()
                    ->whereNotNull('auto_accept_at')
                    ->with('controller')
                    ->where('auto_accept_at','<=',now())
                    ->where('step' , RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER)
                    ->take(10)
                    ->get();
                foreach ($requests as $request) {
                    if ($request->controller) {
                        $request->comments()->create([
                            'user_id' => $request->controller->id,
                            'body' => $message,
                            'display_name' => RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER->title(),
                            'from_status' => RequestStatus::IN_PROGRESS,
                            'to_status' => RequestStatus::IN_PROGRESS,
                            'step' => RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER
                        ]);
                    }
                    $request->message = $message;
                    if (! $request->messages) {
                        $request->messages = [];
                    }
                    $messages = $request->messages;
                    $messages[RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER->value] = $message;
                    $request->messages = $messages;
                    $request->toNextStep()->save();
                }
        }
    }
}
