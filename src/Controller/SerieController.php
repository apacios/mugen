<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Entity\Category;
use App\Provider\ImdbProvider;
use App\Repository\SerieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/serie")
 */
class SerieController extends AbstractController
{
    /**
     * @Route("/show/{id}", name="serie_show", methods={"GET"})
     */
    public function show(Serie $serie, ImdbProvider $imdbProvider): Response
    {
        return $this->render('serie/show.html.twig', [
            'serie' => $serie,
            'data_imdb' => $imdbProvider->search($serie->getName())->getData(),
        ]);
    }

    /**
     * @Route("/list/{id}", name="serie_list", methods={"GET"})
     */
    public function list(Category $category, SerieRepository $serieRepository): Response
    {
        return $this->render('serie/list.html.twig', [
            'category' => $category,
            'serieList' => $serieRepository->findBy(['category' => $category], ['createdAt' => 'DESC']),
        ]);
    }
}
