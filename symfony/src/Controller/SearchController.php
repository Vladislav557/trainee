<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ProductService\ProductService;
use App\Service\ElasticService\ElasticService;

class SearchController extends AbstractController
{
    #[Route('/products', name: 'app_search_all', methods: ['GET'])]
    public function getProducts(ProductService $productService): JsonResponse
    {
        try {

            $response = $productService->getAllProducts();

            if (!empty($response)) {
                return $this->json($response);
            }

            return $this->json([
                'result' => 'Товар не найден'
            ]);

        } catch (\Throwable $th) {
            
            throw new \Exception('Не удалось вернуть все продукты из БД: ' . $th->getMessage());

        }
    }

    #[Route('/products/{sku}', name: 'app_search_by_sku', methods: ['GET'])]
    public function getProductBySku(string $sku, ProductService $productService): JsonResponse
    {
        try {

            $response = $productService->getProductBySku($sku);

            if (!empty($response)) {
                return $this->json($response);
            }

            return $this->json([
                'result' => 'Товар не найден'
            ]);

        } catch (\Throwable $th) {
            
            throw new \Exception('Не удалось вернуть продукт из БД: ' . $th->getMessage());

        }
    }

    #[Route('/products/by-name/{name}', name: 'app_search_by_name', methods: ['GET'])]
    public function getProductsByName(string $name, ElasticService $elasticService): JsonResponse
    {
        try {

            $response = $elasticService->findByName($name);

            if (!empty($response)) {
                return $this->json($response);
            }

            return $this->json([
                'result' => 'Товар не найден'
            ]);

        }catch (\Throwable $th) {
            
            throw new \Exception('Не удалось вернуть продукт из эластика: ' . $th->getMessage());

        }
    }

    #[Route('/products/by-description/{description}', name: 'app_search_by_description', methods: ['GET'])]
    public function getProductsByDescription(string $description, ElasticService $elasticService): JsonResponse
    {
        try {

            $response = $elasticService->findByDescription($description);

            if (!empty($response)) {
                return $this->json($response);
            }

            return $this->json([
                'result' => 'Товар не найден'
            ]);

        }catch (\Throwable $th) {
            
            throw new \Exception('Не удалось вернуть продукт из эластика: ' . $th->getMessage());

        }
    }

    #[Route('/products/by-category/{category}', name: 'app_search_by_category', methods: ['GET'])]
    public function getProductsByCategory(string $category, ProductService $productService): JsonResponse
    {
        try {
            $response = $productService->getProductsByCategory($category);

            if (!empty($response)) {
                return $this->json($response);
            }

            return $this->json([
                'result' => 'Товар не найден'
            ]);

        }catch (\Throwable $th) {
            
            throw new \Exception('Не удалось вернуть продукт из БД: ' . $th->getMessage());

        }
    }
}
