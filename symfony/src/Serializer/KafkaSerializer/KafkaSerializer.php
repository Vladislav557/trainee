<?php

namespace App\Serializer\KafkaSerializer;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Envelope;
use App\Message\KafkaMessage\KafkaMessage;

class KafkaSerializer implements SerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $record = json_decode($encodedEnvelope['body'], true);
        return new Envelope(new KafkaMessage($record));
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();
        return [
            'body' => json_encode([$message->getMessage()])
        ];
    }
}