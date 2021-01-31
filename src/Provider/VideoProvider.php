<?php

namespace App\Provider;

use Nette\Utils\Finder;

class VideoProvider
{
    private string $folder_dir;

    public function __construct()
    {
        $this->folder_dir = $_ENV['VIDEO_DIR'];
    }

    public function getVideoListFromCategory(string $category): array
    {
        $fromPath = $this->folder_dir . $category;
        $videoList = [];

        foreach (Finder::findFiles('*')->from($fromPath) as $file) {
            $fileInformations = [
                'fileName' => \str_replace('.' . $file->getExtension(), '', $file->getBaseName()),
                'baseName' => $file->getBaseName(),
                'pathName' => $file->getPathName(),
                'extension' => $file->getExtension(),
            ];
            $pathInformations = $this->getVideoFolderPathInformations($fromPath, $file->getPath());

            if (empty($pathInformations)) {
                $videoList['root'][] = $fileInformations;
            } else {
                $videoList[$pathInformations['serie']][$pathInformations['season']][] = $fileInformations;
            }
        }

        return $videoList;
    }

    /**
     * getVideoFolderPathInformations
     *
     * @param string $fromPath
     * @param string $filePath
     * @return array
     */
    private function getVideoFolderPathInformations(string $fromPath, string $filePath): array
    {
        $folderName = \ltrim(
            \str_replace($fromPath, '', $filePath),
            '/'
        );

        if ($folderName === '') {
            return [];
        }

        $nameAndSeason = \explode('/', $folderName);

        return [
            'serie' => $nameAndSeason[0],
            'season' => (int) $nameAndSeason[1],
        ];
    }
}
