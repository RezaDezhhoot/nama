<?php

namespace App\Listeners;

use App\Events\ActionNeededReportEvent;
use App\Events\ActionNeededRequestEvent;
use App\Events\ConfirmationReportEvent;
use App\Events\ConfirmationRequestEvent;
use App\Events\RejectReportEvent;
use App\Events\RejectRequestEvent;
use App\Services\Notification\Send;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $user = null;
        $template = null;
        $amount = 0;

        switch (get_class($event)) {
            case ConfirmationRequestEvent::class:
                $event->request->load('user');
                if ($event->request->user) {
                    $user = $event->request->user;
                    $template = config('sms.kaveh_negar.template1');
                    $amount = $event->request->final_amount ?? $event->request->offer_amount ?? $event->request->amount ?? 0;
                }
                break;
            case ActionNeededRequestEvent::class:
                $event->request->load('user');
                if ($event->request->user) {
                    $user = $event->request->user;
                    $template = config('sms.kaveh_negar.template3');
                    $amount = $event->request->final_amount ?? $event->request->offer_amount ?? $event->request->amount ?? 0;
                }
                break;
            case RejectRequestEvent::class:
                $event->request->load('user');
                if ($event->request->user) {
                    $user = $event->request->user;
                    $template = config('sms.kaveh_negar.template2');
                    $amount = $event->request->final_amount ?? $event->request->offer_amount ?? $event->request->amount ?? 0;
                }
                break;
            case ConfirmationReportEvent::class:
                $event->report->load(['request','request.user']);
                if ($event->report->request && $event->report->request->user) {
                    $user = $event->report->request->user;
                    $template = config('sms.kaveh_negar.template4');
                    $amount = $event->report->final_amount ?? $event->report->offer_amount ?? $event->report->amount ?? 0;
                }
                break;
            case ActionNeededReportEvent::class:
                $event->report->load(['request','request.user']);
                if ($event->report->request && $event->report->request->user) {
                    $user = $event->report->request->user;
                    $template = config('sms.kaveh_negar.template6');
                    $amount = $event->report->final_amount ?? $event->report->offer_amount ?? $event->report->amount ?? 0;
                }
                break;
            case RejectReportEvent::class:
                $event->report->load(['request','request.user']);
                if ($event->report->request && $event->report->request->user) {
                    $user = $event->report->request->user;
                    $template = config('sms.kaveh_negar.template5');
                    $amount = $event->report->final_amount ?? $event->report->offer_amount ?? $event->report->amount ?? 0;
                }
                break;
        }

        if ($template && $user) {
            try {
                Send::sendOTPSMS($user->phone,$template, [
                    'token' => $amount,
                    'token20' => $user->name ?? 'مربی گرامی'
                ]);
            } catch (\Exception $exception) {
                report($exception);
            }
        }
    }
}
