<?php
class MediaUtils
{

    public static function getMediaInfo($pdo, $media_id)
    {
        $result = $pdo->select("media", ["media_id" => $media_id]);
        return $result ? $result[0] : [];
    }
}
