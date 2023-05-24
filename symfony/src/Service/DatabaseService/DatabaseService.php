<?php

namespace App\Service\DatabaseService;

use Doctrine\ORM\EntityManagerInterface;

class DatabaseService
{
    public function __construct(private EntityManagerInterface $entityManager, private $connection = null)
    {
        
    }

    private function setConnection(): void
    {
        try {
            
            $this->connection = $this->entityManager->getConnection()->connect();

        } catch (\Throwable $th) {
            
            throw new \Exception('Ошибка при попытке установить соединение с БД: ' . $th->getMessage());

        }
    }

    public function checkConnection(): bool
    {
        $this->setConnection();
        return $this->entityManager->getConnection()->isConnected();
    }
}