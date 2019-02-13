<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("../utilities/Utility.php");
    include_once("./init.php");
    $pdo = new SQLPdo($db);
    $utility = new Utility();
    switch($action) {
        case "list":
            $media = $pdo -> select("media");
            foreach($media as &$image) {
                $image["media_thumb_url"] = $utility -> getThumbName($image["media_url"]);
            }
            echo json_encode($media);
            break;
    }
?>