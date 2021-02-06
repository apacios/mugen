<?php

namespace App\Controller;

use App\Provider\ImdbProvider;
use App\Provider\VideoProvider;
use App\Builder\VideoYamlBuilder;
use App\Provider\CategoryProvider;
use App\Repository\LibraryRepository;
use App\Builder\CategoryYmalBuilder;
use App\Repository\CategoryRepository;
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
    public function index(CategoryRepository $categoryRepository, LibraryRepository $LibraryRepository, VideoProvider $videoProvider): Response
    {
        ($videoProvider->getVideoListFromCategory('series'));
        return $this->render('dashboard/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'videos' => $LibraryRepository->findAll(),
        ]);
    }
}
