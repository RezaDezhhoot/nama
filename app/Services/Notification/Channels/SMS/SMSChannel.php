<?php

namespace App\Services\Notification\Channels\SMS;

use App\Services\Notification\Send;
use Illuminate\Notifications\Notification;

class SMSChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     * @throws \Exception
     */
    public function send($notifiable, Notification $notification): void
    {
        $data = $notification->toSMS($notifiable);
        Send::sendSMS($data['to'] , $data['message']);
    }
}
