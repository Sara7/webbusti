<?php

namespace App\Formatter;

use App\Entity\Media;

class MediaFormatter
{
    /**
     * @param Media $media
     *
     * @return array
     */
    public function format(Media $media): array
    {
        return [
            'media_id'        => $media->getId(),
            'media_format'    => $media->getFormat(),
            'media_title'     => $media->getTitle(),
            'media_url'       => $media->getUrl(),
            'media_thumb_url' => $media->getThumbUrl(),
            'media_category'  => $media->getCategory(),
        ];
    }
}
