<?php

namespace App\Controller;

use App\Entity\Video;
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
    public function show(Video $video, ImdbProvider $imdbProvider): Response
    {
        return $this->render('video/show.html.twig', [
            'video' => $video,
            'data' => $imdbProvider->search('Gurren Lag')->getData(),
        ]);
    }
}
