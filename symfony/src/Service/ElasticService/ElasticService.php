<?php

namespace App\Service\ElasticService;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use App\Service\ProductService\ProductService;

class ElasticService
{
    private Client $client;
    private string $index;
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->index = $_ENV['ELASTICSEARCH_INDEX'];
        $this->client = ClientBuilder::create()
            -> setHosts([$_ENV['ELASTICSEARCH_HOSTS']])
            ->build();
    }

    public function checkConnection(): bool
    {
        return !is_null($this->client->info());
    }

    public function findByName(string $name): array
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'match' => [
                        'name' => [
                            'query' => $name,
                            "fuzziness" => 1
                        ]
                    ]
                ],
                'size' => 10000

            ]
        ];

        try {
            
            $products = $this->client->search($params);
            $result = [];
            foreach ($products['hits']['hits'] as $product) {
                $sku = $product['_source']['product_sku'];

                $result[] = [
                    'id' => $this->productService->getProductBySku($sku)['id'],
                    ...$product['_source']
                ];
            }
            return $result;

        } catch (\Throwable $th) {

            throw new \Exception('Ошибка при поиске записи в elasticsearch: ' . $th->getMessage());

        } 
    }

    public function findByDescription(string $description): array
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'match' => [
                        'detail_text' => [
                            'query' => $description,
                            "fuzziness" => 1
                        ]
                    ]
                ]
            ],
            'size' => 10000
        ];

        try {
            
            $products = $this->client->search($params);
            $result = [];
            foreach ($products['hits']['hits'] as $product) {
                $sku = $product['_source']['product_sku'];

                $result[] = [
                    'id' => $this->productService->getProductBySku($sku)['id'],
                    ...$product['_source']
                ];
            }
            return $result;

        } catch (\Throwable $th) {

            throw new \Exception('Ошибка при поиске записи в elasticsearch: ' . $th->getMessage());

        } 
    }

    public function findAll(): array
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ],
                'size' => 100000
            ]
        ];

        try {
            
            $response = $this->client->search($params);
            return $response['hits']['hits'];

        } catch (\Throwable $th) {

            throw new \Exception('Ошибка при поиске записи в elasticsearch: ' . $th->getMessage());

        } 
    }

    public function add(array $data): bool
    {
        $params = [
            'index' => $this->index,
            'body' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ];

        try {

            if (!$this->isExists($data['product_sku'])) {

                $this->client->index($params);
                return true;
                
            }
            
            return false;

        } catch (\Throwable $th) {
            
            throw new \Exception('Ошибка при добалвении нового продукта в elastic');

        }
    }

    private function isExists(string $sku): bool
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'match' => [
                        'product_sku' => $sku
                    ]
                ]
            ]
        ];

        try {
            
            $response = $this->client->search($params);
            return !empty($response['hits']['hits']);

        } catch (\Throwable $th) {

            throw new \Exception('Ошибка при поиске записи в elasticsearch: ' . $th->getMessage());

        } 
    }
}