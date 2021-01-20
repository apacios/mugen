<?php

namespace App\Helper;

use Nette\Utils\Finder;

class VideoHelper
{
    private string $folder_dir;

    public function __construct()
    {
        $this->folder_dir = $_ENV['VIDEO_DIR'];
    }

    /**
     * Get list of videos from source folder
     *
     * @return array
     */
    public function getVideoList(): array
    {
        $videoList = [];

        foreach (Finder::findFiles('*')->from($this->folder_dir) as $file) {
            $videoList[$file->getPath()][] = $file;
        }

        return $videoList;
    }
}
