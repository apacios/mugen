<?php

namespace App\Handler;

use App\Entity\Serie;
use App\Entity\Library;
use DateTimeImmutable;
use App\Entity\Category;
use App\Provider\ImdbProvider;
use Doctrine\ORM\EntityManagerInterface;

class VideoHandler
{
    protected EntityManagerInterface $em;
    protected ImdbProvider $imdbProvider;

    public function __construct(EntityManagerInterface $em, ImdbProvider $imdbProvider)
    {
        $this->em = $em;
        $this->imdbProvider = $imdbProvider;
    }

    public function saveVideoList(Category $category, array $videoList): bool
    {
        if (empty($videoList)) {
            return true;
        }

        if ('featured_film' === $category->getType()) {
            $this->saveVideoListInLibrary($category, $videoList);
        }

        if ('serie' === $category->getType()) {
            foreach ($videoList as $serieName => $seasons) {
                foreach ($seasons as $seasonNumber => $videos) {
                    $this->saveVideoListInLibrary(
                        $category,
                        $videos,
                        $this->saveSerie($serieName, (int) $seasonNumber)
                    );
                }
            }
        }

        return true;
    }

    private function saveVideoListInLibrary(Category $category, array $videoList, Serie $serie = null): void
    {
        foreach ($videoList as $video) {
            $videoExists = (bool) $this->em->getRepository(Library::class)->findOneBy(
                ['path' => $video['pathName']]
            );

            if (true === $videoExists) {
                continue;
            }

            $library = (new Library())
                ->setName($this->addSpacesInName($video['fileName']))
                ->setFileName($video['baseName'])
                ->setCategory($category)
                ->setPath($video['pathName'])
                ->setActive(true)
                ->setCreatedAt(new DateTimeImmutable('now'))
                ->setUpdatedAt(new DateTimeImmutable('now'));

            if ('serie' === $category->getType()) {
                $library
                    ->setSerie($serie)
                    ->setEpisode((int) $video['episode']);
            }

            $this->em->persist($library);
            $this->em->flush();
            $this->createVideoThumbnail($category, $library);
        }
    }

    private function saveSerie(string $serieName, int $seasonNumber): Serie
    {
        $serieName = $this->addSpacesInName($serieName);

        $serie = $this->em->getRepository(Serie::class)->findOneBy(
            ['name' => $serieName, 'season' => $seasonNumber]
        );

        if (true === (bool) $serie) {
            return $serie;
        }

        $serie = (new Serie())
            ->setName($serieName)
            ->setSeason($seasonNumber)
            ->setActive(true)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setUpdatedAt(new DateTimeImmutable('now'));
        $this->em->persist($serie);
        $this->em->flush();

        return $serie;
    }

    private function addSpacesInName(string $name): string
    {
        return ltrim(
            preg_replace('/(?<!\ )[A-Z]/', ' $0', $name)
        );
    }

    private function createVideoThumbnail(Category $category, Library $library): bool
    {
        if ('serie' === $category->getType()) {
            return $this->imdbProvider
                ->search(
                    $library->getSerie()->getName()
                )
                ->saveMainPhoto($library->getId());
        }

        return $this->imdbProvider
            ->search($library->getName())
            ->saveMainPhoto($library->getId());
    }
}
