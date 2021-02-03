<?php

namespace App\Controller;

use App\Provider\ImdbProvider;
use App\Provider\VideoProvider;
use App\Builder\VideoYamlBuilder;
use App\Provider\CategoryProvider;
use App\Repository\VideoRepository;
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
    public function index(CategoryRepository $categoryRepository, VideoRepository $videoRepository, ImdbProvider $imdbProvider): Response
    {
        dump($imdbProvider->search('matrix')->getData());
        return $this->render('dashboard/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'videos' => $videoRepository->findAll(),
        ]);
    }
}
