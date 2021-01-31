<?php

namespace App\Handler;

use App\Entity\Serie;
use App\Entity\Video;
use DateTimeImmutable;
use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class VideoHandler
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
                        ->setName($serieName)
                        ->setSeason($seasonNumber)
                        ->setSynopsis('blah blah blah')
                        ->setThumbnail('none')
                        ->setActive(true)
                        ->setCreatedAt(new DateTimeImmutable('now'))
                        ->setUpdatedAt(new DateTimeImmutable('now'));
                    $this->entityManager->persist($serie);
                    $this->entityManager->flush();
                }

                if (empty($videos[0])) {
                    $video = $this->entityManager->getRepository(Video::class)->findOneBy(
                        ['path' => $videos['pathName']]
                    );

                    if (null !== $video) {
                        continue;
                    }

                    $newVideo = (new Video())
                        ->setName($videos['fileName'])
                        ->setFileName($videos['baseName'])
                        ->setThumbnail('none')
                        ->setCategories($category)
                        ->setSynopsis('blah blah blah blah bli')
                        ->setSerie($serie)
                        ->setPath($videos['pathName'])
                        ->setActive(true)
                        ->setCreatedAt(new DateTimeImmutable('now'))
                        ->setUpdatedAt(new DateTimeImmutable('now'));
                    $this->entityManager->persist($newVideo);
                    $this->entityManager->flush();
                    continue;
                }

                foreach ($videos as $videoData) {
                    $video = $this->entityManager->getRepository(Video::class)->findOneBy(
                        ['path' => $videoData['pathName']]
                    );

                    if (null !== $video) {
                        continue;
                    }

                    $newVideo = (new Video())
                        ->setName($videoData['fileName'])
                        ->setFileName($videoData['baseName'])
                        ->setThumbnail('none')
                        ->setCategories($category)
                        ->setSynopsis('blah blah blah blah bli')
                        ->setSerie($serie)
                        ->setPath($videoData['pathName'])
                        ->setActive(true)
                        ->setCreatedAt(new DateTimeImmutable('now'))
                        ->setUpdatedAt(new DateTimeImmutable('now'));
                    $this->entityManager->persist($newVideo);
                    $this->entityManager->flush();
                }
            }
        }

        return true;
    }
}
