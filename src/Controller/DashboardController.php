<?php

namespace App\Controller;

use App\Provider\VideoProvider;
use App\Provider\CategoryProvider;
use App\Builder\CategoryYmalBuilder;
use App\Builder\VideoYamlBuilder;
use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
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
    public function index(CategoryRepository $categoryRepository, VideoRepository $videoRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'videos' => $videoRepository->findAll(),
        ]);
    }
}
