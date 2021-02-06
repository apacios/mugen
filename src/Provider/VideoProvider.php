<?php

namespace App\Provider;

use App\Entity\Category;
use Nette\Utils\Finder;

class VideoProvider
{
    private string $folder_dir;

    public function __construct()
    {
        $this->folder_dir = $_ENV['VIDEO_DIR'];
    }

    /**
     * Found all videos available for a given category
     *
     * @param Category $category
     * @return array
     */
    public function getVideoListFromCategory(Category $category): array
    {
        $videoList = [];

        foreach (Finder::findFiles('*')->from($category->getPath()) as $file) {
            $fileInformations = [
                'fileName' => \str_replace('.' . $file->getExtension(), '', $file->getBaseName()),
                'baseName' => $file->getBaseName(),
                'pathName' => $this->getRelativePath($file->getPathName()),
            ];

            if ('featured_film' === $category->getType()) {
                $videoList[] = $fileInformations;
            }

            if ('serie' === $category->getType()) {
                $pathInformations = $this->getSerieInformations($category->getPath(), $file->getPath(), $file->getPathName());
                $fileInformations['episode'] = $pathInformations['episode'];
                $videoList[$pathInformations['serie']][$pathInformations['season']][] = $fileInformations;
            }
        }

        return $videoList;
    }

    /**
     * Get informations for the serie from the video name (ex: S01E01)
     *
     * @param string $fromPath
     * @param string $filePath
     * @return array
     */
    private function getSerieInformations(string $fromPath, string $folderPath, string $filePath): array
    {
        $folderName = \ltrim(
            \str_replace($fromPath, '', $folderPath),
            '/'
        );

        if ($folderName === '') {
            return [];
        }

        preg_match('/(S\d{1,2})(E\d{1,3})/s', $filePath, $matches);

        return [
            'serie' => $folderName,
            'season' => ltrim($matches[1], 'S'),
            'episode' => ltrim($matches[2], 'E'),
        ];
    }

    public function getRelativePath(string $path): string
    {
        return \str_replace($this->folder_dir, '/library/', $path);
    }
}
