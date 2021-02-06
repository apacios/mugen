<?php

namespace App\Handler;

use App\Builder\ThumbnailVideoBuilder;
use App\Entity\Serie;
use App\Entity\Library;
use DateTimeImmutable;
use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class VideoHandler
{
    protected EntityManagerInterface $entityManager;
    protected ThumbnailVideoBuilder $thumbnailVideoBuilder;

    public function __construct(EntityManagerInterface $entityManager, ThumbnailVideoBuilder $thumbnailVideoBuilder)
    {
        $this->entityManager = $entityManager;
        $this->thumbnailVideoBuilder = $thumbnailVideoBuilder;
    }

    public function saveVideoList(string $categoryName, array $videoList): bool
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(
            ['name' => $categoryName]
        );

        foreach ($videoList as $serieName => $seasons) {
            foreach ($seasons as $seasonNumber => $videos) {

                $serie = $this->entityManager->getRepository(Serie::class)->findOneBy(
                    ['name' => $serieName, 'season' => $seasonNumber]
                );

                if (null === $serie) {
                    $serie = (new Serie())
                        ->setName($this->addSpacesInName($serieName))
                        ->setSeason($seasonNumber)
                        ->setActive(true)
                        ->setCreatedAt(new DateTimeImmutable('now'))
                        ->setUpdatedAt(new DateTimeImmutable('now'));
                    $this->entityManager->persist($serie);
                    $this->entityManager->flush();
                }

                if (empty($videos[0])) {
                    $video = $this->entityManager->getRepository(Library::class)->findOneBy(
                        ['path' => $videos['pathName']]
                    );

                    if (null !== $video) {
                        continue;
                    }

                    $newVideo = (new Library())
                        ->setName($videos['fileName'])
                        ->setFileName($videos['baseName'])
                        ->setCategory($category)
                        ->setSerie($serie)
                        ->setEpisode($videos['episode'])
                        ->setPath($videos['pathName'])
                        ->setActive(true)
                        ->setCreatedAt(new DateTimeImmutable('now'))
                        ->setUpdatedAt(new DateTimeImmutable('now'));
                    $this->entityManager->persist($newVideo);
                    $this->entityManager->flush();
                    // $this->thumbnailVideoBuilder->generate($newVideo->getId(), $newVideo->getPath());
                    continue;
                }

                foreach ($videos as $videoData) {
                    $video = $this->entityManager->getRepository(Library::class)->findOneBy(
                        ['path' => $videoData['pathName']]
                    );

                    if (null !== $video) {
                        continue;
                    }

                    $newVideo = (new Library())
                        ->setName($videoData['fileName'])
                        ->setFileName($videoData['baseName'])
                        ->setCategory($category)
                        ->setSerie($serie)
                        ->setEpisode($videoData['episode'])
                        ->setPath($videoData['pathName'])
                        ->setActive(true)
                        ->setCreatedAt(new DateTimeImmutable('now'))
                        ->setUpdatedAt(new DateTimeImmutable('now'));
                    $this->entityManager->persist($newVideo);
                    $this->entityManager->flush();
                    // $this->thumbnailVideoBuilder->generate($newVideo->getId(), $newVideo->getPath());
                }
            }
        }

        return true;
    }

    private function addSpacesInName(string $name)
    {
        return ltrim(
            preg_replace('/(?<!\ )[A-Z]/', ' $0', $name)
        );
    }
}
