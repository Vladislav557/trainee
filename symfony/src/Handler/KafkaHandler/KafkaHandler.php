<?php

namespace App\Handler\KafkaHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\KafkaMessage\KafkaMessage;
use App\Service\ProductService\ProductService;
use App\Service\CategoryService\CategoryService;
use App\Service\DatabaseService\DatabaseService;
use App\Service\ElasticService\ElasticService;


#[AsMessageHandler]
class KafkaHandler
{
    public function __construct(private ProductService $productService, private CategoryService $categoryService, private DatabaseService $databaseService, private ElasticService $elasticService)
    {
        
    }

    public function __invoke(KafkaMessage $message): void
    {
        try {
            
            if ($this->databaseService->checkConnection()) {
                $incomingMessage = $message->getMessage()[0];

                $categoryName = $incomingMessage['level_3'] ?? $incomingMessage['level_2'] ?? $incomingMessage['level_1'] ?? null;
                $category = null;

                if (!is_null($categoryName)) {
                    $this->categoryService->createCategory($categoryName);
                    $category = $this->categoryService->getCategoryByName($categoryName);
                }

                $this->productService->createProduct($incomingMessage, $category);

                print_r('Продукт успешно добавлен: ' . $incomingMessage['product_sku'] . PHP_EOL);

                if ($this->elasticService->checkConnection()) {
                    $product = [
                        'product_sku' => $incomingMessage['product_sku'],
                        'name' => $incomingMessage['name'],
                        'price' => $incomingMessage['price'],
                        'detail_text' => $incomingMessage['detail_text'],
                        'category' => $categoryName
                    ];
    
                    if ($this->elasticService->add($product)) {
                        print_r($incomingMessage['product_sku'] . " Добавлен в elastic" . PHP_EOL);
                    }
                }
            }

        } catch (\Throwable $th) {

            throw new \Exception('Ошибка добавление нового продукта в хендлере: ' . $th->getMessage());
            
        }
    }
}