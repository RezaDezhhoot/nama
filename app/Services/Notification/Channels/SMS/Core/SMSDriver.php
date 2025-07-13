<?php

namespace App\Services\Notification\Channels\SMS\Core;

interface SMSDriver
{
    public function message($message): static;

    public function to(...$to): static;

    public function send();

    public function sendOTP($temp , $args = []);
}
