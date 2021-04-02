<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\LibraryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/featuredfilm")
 */
class FeaturedFilmController extends AbstractController
{
    /**
     * @Route("/list/{id}", name="featuredfilm_list", methods={"GET"})
     */
    public function list(Category $category, LibraryRepository $libraryRepository): Response
    {
        return $this->render('featured_film/list.html.twig', [
            'category' => $category,
            'libraryList' => $libraryRepository->findBy(['category' => $category], ['updatedAt' => 'DESC']),
        ]);
    }
}
