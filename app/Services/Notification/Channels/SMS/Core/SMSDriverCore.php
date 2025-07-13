<?php

namespace App\Services\Notification\Channels\SMS\Core;

use App\Services\Notification\Channels\SMS\Core\SMSDriver as SMSDriverInterface;

abstract class SMSDriverCore implements SMSDriverInterface
{
    protected ?string $message = null;

    protected ?array $to = [];
    abstract public function send();

    abstract public function sendOTP($temp , $args = []);

    public function __construct(){}

    public function message($message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function to(...$to): static
    {
        $this->to = $to;

        return $this;
    }

    public function getNumbers(): ?array
    {
        return $this->to;
    }
}
