<?php

namespace App\Services\Notification\Channels\SMS\Senders;

use App\Services\Notification\Channels\SMS\Core\SMSDriver;

class SendSMS
{
    public static function make(SMSDriver|string $SMSDriver): SMSDriver
    {
        return $SMSDriver instanceof SMSDriver ? $SMSDriver : new $SMSDriver;
    }
}
