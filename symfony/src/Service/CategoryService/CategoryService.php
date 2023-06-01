<?php

namespace App\Service\CategoryService;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoryService
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
        
    }

    public function createCategory(string $title): void
    {
        try {
            
            if (!$this->categoryRepository->isExists($title)) {
                
                $category = new Category();
                $category->setTitle($title);

                $this->categoryRepository->save($category, true);

            }

        } catch (\Throwable $th) {
            
            throw new \Exception('Ошибка при добавлении новой категории: ' . $th->getMessage());

        }
    }

    public function getCategoryByName(string $title): ?Category
    {
        try {
            
            if ($this->categoryRepository->isExists($title)) {

                return $this->categoryRepository
                    ->findBy(['title' => $title], [])[0];

            }

            return null;

        } catch (\Throwable $th) {
            
            throw new \Exception('Ошибка при поиске категории: ' . $th->getMessage());

        }
    }
}