<?php

namespace App\Service\ProductService;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CategoryService\CategoryService;

class ProductService
{
    public function __construct(private ProductRepository $productRepository, private ?CategoryService $categoryService)
    {
        
    }

    public function createProduct(array $data, Category $category): void
    {
        try {

            [
                'product_sku' => $productSku,
                'price' => $price,
                'name' => $productName,
                'detail_text' => $detailText
            ] = $data;

            if (!$this->productRepository->isExists($productSku)) {

                $product = new Product();
                $product->setProductSku($productSku);
                $product->setName($productName);
                $product->setDetailText($detailText);
                $product->setPrice($price);
                $product->setCategory($category);

                $this->productRepository->save($product, true);
            }

        } catch (\Throwable $th) {
            
            throw new \Exception('Ошибка при добавлении нового продукта в БД: ' . $th->getMessage());

        }
    }

    public function getProductBySku(string $sku): ?array
    {
        try {
            
            $product = $this    
                ->productRepository
                ->findBy(['product_sku' => $sku], []);

                if ($product) {
                    return $this->toArray(...$product);
                }

                return null;

        } catch (\Throwable $th) {
            
            throw new \Exception('Ошибка при поиске продукта в БД: ' . $th->getMessage());

        }
    }

    public function getAllProducts(): array
    {
        try {
            
            $products = $this->productRepository->findAll();
            $result = [];

            foreach($products as $product) {
                $result[] = $this->toArray($product);
            }
            return $result ;

        } catch (\Throwable $th) {
            
            throw new \Exception('Ошибка при возврате всех продуктов из БД: ' . $th->getMessage());

        }
    }

    public function getProductsByCategory(string $category): array
    {
        try {
            $categoryEntity = $this->categoryService->getCategoryByName($category);
            $products = $this->productRepository->findBy(['category' => $categoryEntity], []);
            $result = [];

            foreach($products as $product) {
                $result[] = $this->toArray($product);
            }
            
            return $result;

        } catch (\Throwable $th) {
            
            throw new \Exception('Ошибка при возврате всех продуктов из БД: ' . $th->getMessage());

        }
    }

    private function toArray(Product $product): array
    {
        return [
            'product_sku' => $product->getProductSku(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'detail_text' => $product->getDetailText(),
            'category' => $product->getCategory()->getTitle()
        ];
    }
}