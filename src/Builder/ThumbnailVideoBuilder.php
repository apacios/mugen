<?php

namespace App\Builder;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

class ThumbnailVideoBuilder
{
    const IMAGE_FOLDER = 'public/thumbnails/';

    public function generate(int $imageName, string $videoSource)
    {
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($videoSource);
        $video
            ->frame(TimeCode::fromSeconds(42))
            ->save(self::IMAGE_FOLDER . $imageName . '.jpg');
    }
}
