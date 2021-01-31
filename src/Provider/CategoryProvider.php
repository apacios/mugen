<?php

namespace App\Provider;

class CategoryProvider
{
    private string $folder_dir;

    public function __construct()
    {
        $this->folder_dir = $_ENV['VIDEO_DIR'];
    }

    public function getCategoryList(): array
    {
        $categoryList = \scandir($this->folder_dir);

        // Get all first folders to get categories
        foreach ($categoryList as $key => $category) {
            if (\str_starts_with($category, '.')) {
                unset($categoryList[$key]);
            }
        }

        // Reorder array
        return array_values($categoryList);
    }
}
