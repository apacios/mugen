<?php

namespace App\DataFixtures;

use App\Entity\Category;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = ['films', 'series'];
        $categoryTypes = ['featured_film', 'serie'];
        $categoryIcon = ['movie', 'tv'];

        foreach ($categories as $key => $categoryName) {
            $category = new Category();
            $category
                ->setName($categoryName)
                ->setPath('/var/www/html/public/library/' . $categoryName . '/')
                ->setIcon($categoryIcon[$key])
                ->setType($categoryTypes[$key])
                ->setCreatedAt(new \DateTimeImmutable('NOW'));
                $manager->persist($category);
        }

        $manager->flush();
    }
}
