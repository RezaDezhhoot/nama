<?php

namespace App\Services\Notification\Channels\SMS\Drivers;

use App\Services\Notification\Channels\SMS\Core\SMSDriverCore;
use Illuminate\Support\Facades\Http;

class KavehNegarDriver extends SMSDriverCore
{

    #[\Override] public function send()
    {
        $api_key = config('sms.kaveh_negar.api_key');
        $url =  config('sms.kaveh_negar.api_url').'/'.$api_key.'/sms/send.json';
        $data = [
            'receptor' => $this->to[0],
            'message' => $this->message
        ];
        return json_decode(Http::get($url, $data)->body() , true);
    }

    #[\Override] public function sendOTP($temp , $args = [])
    {
        $api_key = config('sms.kaveh_negar.api_key');
        $url = config('sms.kaveh_negar.api_url').'/'.$api_key.'/verify/lookup.json';
        $data = [
            'receptor' => $this->to[0],
            'template' => $temp,
            ...$args,
        ];
        return json_decode(Http::get($url, $data)->body() , true);
    }
}
