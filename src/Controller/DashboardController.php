<?php

namespace App\Controller;

use App\Repository\LibraryRepository;
use App\Repository\CategoryRepository;
use App\Repository\SerieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard_index")
     */
    public function index(
        CategoryRepository $categoryRepository,
        LibraryRepository $libraryRepository,
        SerieRepository $serieRepository
    ): Response
    {
        $library = [];

        foreach ($categoryRepository->findAll() as $key => $category) {
            if ('featured_film' === $category->getType()) {
                $library[$key] = [
                    'type' => 'featured_film',
                    'category' => $category->getName(),
                    'library' => $libraryRepository->findBy(
                        ['category' => $category],
                        ['createdAt' => 'DESC'],
                        10
                    )
                ];
            }

            if ('serie' === $category->getType()) {
                $library[$key] = [
                    'type' => 'serie',
                    'category' => $category->getName(),
                    'library' => $serieRepository->findByCategoryByLastestUpdated($category)
                ];
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'library' => $library,
        ]);
    }
}
