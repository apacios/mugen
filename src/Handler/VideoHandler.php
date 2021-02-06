<?php

namespace App\Handler;

use App\Entity\Serie;
use App\Entity\Library;
use DateTimeImmutable;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class VideoHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function saveVideoList(Category $category, array $videoList): bool
    {
        if (empty($videoList)) {
            return true;
        }

        if ('featured_film' === $category->getType()) {
            $this->saveVideoListInLibrary($category, $videoList);
            $this->em->flush();
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
            $this->em->flush();
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
                return;
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
        }
    }

    private function saveSerie(string $serieName, int $seasonNumber): Serie
    {
        $serie = $this->em->getRepository(Serie::class)->findOneBy(
            ['name' => $serieName, 'season' => $seasonNumber]
        );

        if (!empty($serie)) {
            return $serie;
        }

        $serie = (new Serie())
            ->setName($this->addSpacesInName($serieName))
            ->setSeason($seasonNumber)
            ->setActive(true)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setUpdatedAt(new DateTimeImmutable('now'));
        $this->em->persist($serie);

        return $serie;
    }

    private function addSpacesInName(string $name): string
    {
        return ltrim(
            preg_replace('/(?<!\ )[A-Z]/', ' $0', $name)
        );
    }
}
