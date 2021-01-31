<?php

namespace App\Handler;

use DateTimeImmutable;
use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class CategoryHandler
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveCategory(string $categoryName): bool
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(
            ['name' => $categoryName]
        );

        if (null !== $category) {
            return true;
        }

        $folderDir = $_ENV['VIDEO_DIR'];

        $category = (new Category())
            ->setName($categoryName)
            ->setPath($folderDir . $categoryName)
            ->setIcon('smart_display')
            ->setCreatedAt(new DateTimeImmutable('now'));

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return true;
    }
}
