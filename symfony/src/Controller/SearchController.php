<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ProductService\ProductService;
use App\Service\ElasticService\ElasticService;
use Symfony\Component\HttpFoundation\Request;
use App\Enum\Status;

class SearchController extends AbstractController
{
    #[Route('/products', name: 'app_search_all', methods: ['GET'])]
    public function getProducts(ProductService $productService): JsonResponse
    {
        $result = [
            'status_code' => Status::BAD_REQUEST,
            'content' => 'Товар не найден',
            'error' => null
        ];

        try {

            $response = $productService->getAllProducts();

            if (!empty($response)) {
                $result['status_code'] = Status::OK;
                $result['content'] = $response;
            } else {
                $result['status_code'] = Status::OK;
            }

        } catch (\Throwable $th) {
            
            $result['error'] = 'Ошибка при возвращении списка товаров ' . $th->getMessage();

        }

        return $this->json($result);
    }

    #[Route('/products/search-sku', name: 'app_search_by_sku', methods: ['GET'])]
    public function getProductBySku(ProductService $productService, Request $request): JsonResponse
    {
        $sku = $request->query->get('sku');

        $result = [
            'status_code' => Status::BAD_REQUEST,
            'content' => 'Товар не найден',
            'error' => null
        ];


        try {

            $response = $productService->getProductBySku($sku);

            if (!empty($response)) {
                $result['status_code'] = Status::OK;
                $result['content'] = $response;
            } else {
                $result['status_code'] = Status::NOT_FOUND;
            }


        } catch (\Throwable $th) {
            
            $result['error'] = 'Ошибка при возвращении списка товаров ' . $th->getMessage();

        }

        return $this->json($result);
    }

    #[Route('/products/search-name', name: 'app_search_by_name', methods: ['GET'])]
    public function getProductsByName(ElasticService $elasticService, Request $request): JsonResponse
    {
        $name = $request->query->get('name');

        $result = [
            'status_code' => Status::BAD_REQUEST,
            'content' => 'Товар не найден',
            'error' => null
        ];

        try {

            $response = $elasticService->findByName($name);

            if (!empty($response)) {
                $result['content'] = $response;
                $result['status_code'] = Status::OK;
            } else {
                $result['status_code'] = Status::NOT_FOUND;
            }

        }catch (\Throwable $th) {
            
            $result['error'] = "Ошибка при поиске товара по имени: {$th->getMessage()}";

        }

        return $this->json($result);
    }

    #[Route('/products/search-description', name: 'app_search_by_description', methods: ['GET'])]
    public function getProductsByDescription(ElasticService $elasticService, Request $request): JsonResponse
    {
        $description = $request->query->get('description');

        $result = [
            'status_code' => Status::BAD_REQUEST,
            'content' => 'Товар не найден',
            'error' => null
        ];

        try {

            $response = $elasticService->findByDescription($description);

            if (!empty($response)) {
                $result['content'] = $response;
                $result['status_code'] = Status::OK;
            } else {
                $result['status_code'] = Status::NOT_FOUND;
            }

        } catch (\Throwable $th) {
            
            $result['error'] = "Ошибка при поиске товара по описанию: {$th->getMessage()}";

        }

        return $this->json($result);
    }

    #[Route('/products/search-category', name: 'app_search_by_category', methods: ['GET'])]
    public function getProductsByCategory(ProductService $productService, Request $request): JsonResponse
    {
        $category = $request->query->get('category');

        $result = [
            'status_code' => Status::BAD_REQUEST,
            'content' => 'Товар не найден',
            'error' => null
        ];

        try {
            $response = $productService->getProductsByCategory($category);

            if (!empty($response)) {
                $result['content'] = $response;
                $result['status_code'] = Status::OK;
            } else {
                $result['status_code'] = Status::NOT_FOUND;
            }

        } catch (\Throwable $th) {
            
            $result['error'] = "Ошибка при поиске товара по категории: {$th->getMessage()}";

        }

        return $this->json($result);
    }
}
