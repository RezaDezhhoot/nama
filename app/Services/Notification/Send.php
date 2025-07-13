<?php

namespace App\Services\Notification;

use App\Services\Notification\Channels\SMS\Drivers\SMSIRDriver;
use App\Services\Notification\Channels\SMS\Senders\SendSMS;

class Send
{
    public static function sendOTPSMS($to , $temp , $args = [] , $driver = null)
    {
        return SendSMS::make($driver ?? config('sms.default_driver',SMSIRDriver::class))
            ->to($to)
            ->sendOTP($temp , $args);
    }

    public static function sendSMS($to , $code , $driver = null)
    {
        return SendSMS::make($driver ?? config('sms.default_driver',SMSIRDriver::class))
            ->to($to)
            ->message($code)
            ->send();
    }
}
