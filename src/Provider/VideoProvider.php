<?php

namespace App\Provider;

use FFMpeg\FFMpeg;
use Nette\Utils\Finder;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\Dimension;

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
                'pathName' => $this->getRelativePath($file->getPathName()),
                'extension' => $file->getExtension(),
                'episode' => 0,
            ];
            $pathInformations = $this->getVideoFolderPathInformations($fromPath, $file->getPath(), $file->getPathName());

            if (empty($pathInformations)) {
                $videoList['root'][] = $fileInformations;
            } else {
                $fileInformations['episode'] = $pathInformations['episode'];
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
    private function getVideoFolderPathInformations(string $fromPath, string $folderPath, string $filePath): array
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

    public function getRelativePath(string $path)
    {
        return \str_replace($this->folder_dir, '/library/', $path);
    }
}
