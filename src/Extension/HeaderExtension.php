<?php

namespace App\Extension;

use App\Repository\CategoryRepository;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class HeaderExtension extends AbstractExtension
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getCategories', [$this, 'getCategories']),
        ];
    }

    public function getCategories(): array
    {
        return $this->categoryRepository->findAll();
    }
}
