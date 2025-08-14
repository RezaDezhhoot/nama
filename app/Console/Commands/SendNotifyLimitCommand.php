<?php

namespace App\Console\Commands;

use App\Enums\RequestStatus;
use App\Enums\RequestStep;
use App\Models\Report;
use App\Models\Request;
use App\Services\Notification\Send;
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
        $template = config('sms.kaveh_negar.template7');
        switch ($this->option('target')) {
            case "reports":
                $reports = Report::query()
                    ->with('controller2')
                    ->whereNotNull(['next_notify_at','notify_period'])
                    ->where('next_notify_at','<=',now())
                    ->where('status',RequestStatus::PENDING)
                    ->where('step' , RequestStep::APPROVAL_AREA_INTERFACE)
                    ->take(3)
                    ->get();
                foreach ($reports as $report) {
                    $user = $report->controller2;
                    if ($user) {
                        try {
                            Send::sendOTPSMS($user->phone, $template, [
                                'token' => "نما",
                                'token20' => $user->name ?? 'کاربر گرامی'
                            ]);
                            $report->update([
                                'next_notify_at' => now()->addHours($report->notify_period)
                            ]);
                        } catch (\Exception $exception) {
                            report($exception);
                        }
                    }
                }
                break;
            default:
                $requests = Request::query()
                    ->with('controller2')
                    ->whereNotNull(['next_notify_at','notify_period'])
                    ->where('next_notify_at','<=',now())
                    ->where('status',RequestStatus::PENDING)
                    ->where('step' , RequestStep::APPROVAL_AREA_INTERFACE)
                    ->take(3)
                    ->get();
                foreach ($requests as $request) {
                    $user = $request->controller2;
                    if ($user) {
                        try {
                            Send::sendOTPSMS($user->phone, $template, [
                                'token' => "نما",
                                'token20' => $user->name ?? 'کاربر گرامی'
                            ]);
                            $request->update([
                                'next_notify_at' => now()->addHours($request->notify_period)
                            ]);
                        } catch (\Exception $exception) {
                            report($exception);
                        }
                    }
                }
        }
    }
}
