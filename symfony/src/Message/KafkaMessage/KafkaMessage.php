<?php

namespace App\Message\KafkaMessage;

class KafkaMessage
{
    public function __construct(private readonly array $message)
    {}

    public function getMessage(): array
    {
        return $this->message;
    }
}