<?php

namespace App\Services\Notification\Channels\SMS\Drivers;

use App\Services\Notification\Channels\SMS\Core\SMSDriverCore;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\GuzzleException;

class SMSIRDriver extends SMSDriverCore
{
    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function send()
    {
        $result = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' =>  config('sms.smsir.api_key')
            ])
            ->post(config('sms.smsir.base_url').'/bulk' , [
                'lineNumber' => config('site.sms.smsir.line_number'),
                'MessageText' => $this->getMessage(),
                'Mobiles' => $this->getNumbers()
            ]);

        if (! $result->ok()) {
            throw new \Exception($result->body());
        }

        return json_decode($result->body() , true);
    }

    /**
     * @throws \Exception
     */
    public function sendOTP($temp , $args = [])
    {
        $result = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' =>  config('sms.smsir.api_key')
            ])
            ->post(config('sms.smsir.base_url').'/verify' , [
                'TemplateId' => $temp,
                'Mobile' => implode(',',$this->getNumbers()),
                'Parameters' => $args
            ]);

        if (! $result->ok()) {
            throw new \Exception($result->body());
        }

        return json_decode($result->body() , true);
    }
}
