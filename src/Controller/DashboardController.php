<?php

namespace App\Controller;

use App\Repository\LibraryRepository;
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
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
