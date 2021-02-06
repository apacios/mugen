<?php

namespace App\Controller;

use App\Entity\Library;
use App\Provider\ImdbProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/video")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/{id}", name="video_show", methods={"GET"})
     */
    public function show(Library $video, ImdbProvider $imdbProvider): Response
    {
        $imdbSearch = '';

        if ('serie' === $video->getCategory()->getType()) {
            $imdbSearch = $video->getSerie()->getName();
        }

        if ('featured_film' === $video->getCategory()->getType()) {
            $imdbSearch = $video->getName();
        }

        return $this->render('video/show.html.twig', [
            'video' => $video,
            'data' => $imdbProvider->search($imdbSearch)->getData(),
        ]);
    }
}
