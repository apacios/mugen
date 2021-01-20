<?php

namespace App\Controller;

use App\Helper\VideoHelper;
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
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'videoList' => (new VideoHelper())->getVideoList(),
            'controller_name' => 'DashboardController',
        ]);
    }
}
