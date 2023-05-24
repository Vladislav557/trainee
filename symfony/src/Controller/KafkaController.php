<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Message\KafkaMessage\KafkaMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\Request;

class KafkaController extends AbstractController
{
    #[Route('/kafka', name: 'app_kafka')]
    public function index(Request $request, MessageBusInterface $bus): void
    {
        $incomingData = json_decode($request->query->get('incomingProducts'), true);

        try {
            foreach ($incomingData as $product) {
                if (!is_null($product)) {
                    $message = new KafkaMessage($product);
                    $bus->dispatch($message);
                }
            }
        } catch (\Throwable $th) {
            throw new \Exception('Ошибка при отправки данных из KafkaController в Kafka');
        }
    }
}
