<?php

use DataAccess\Dao\SQLPdo;
    

    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("../utilities/Utility.php");
    include_once("./init.php");
    $pdo = new SQLPdo($db->getPdo());
    $utility = new Utility();
    switch($action) {
        case "list":
            $media = $pdo -> select("media");
            // foreach($media as &$image) {
            //     $image["media_thumb_url"] = $utility -> getThumbName($image["media_url"]);
            // }
            echo json_encode($media);
            break;
    }

    class Media {
        public static function getThumbnail(&$media) {
            if(array_key_exists("media_url")) {
                //$media["media_thumb_url"] = $utility -> getThumbName($media["media_url"]);
            }
        }
    }
?>