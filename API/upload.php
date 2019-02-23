<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/media.php";
require_once __DIR__ . "/../utilities/Utility.php";
define("RESOURCES_PATH", __DIR__ . "/../resources/media");
$pdo = new SQLPdo($db);
$utility = new Utility();
    
    if (sizeOf($_FILES) > 0 && isset($httpData["path"])) {
        $ids = [];
        foreach ($_FILES as $file) {
            $tempFilePath = $file["tmp_name"];

            $filePath = $httpData["path"] . "/" . $file["name"];

            $url = buildFilePath($filePath);
            
            if (move_uploaded_file($file["tmp_name"], $url)) {
                $destPath = $GLOBALS['utility'] -> getThumbName($url);
                //resize2($url, 300, 300);
                createThumb($url);
                $data = ["media_url" => $filePath, "media_title" => $file["name"]];
                if(isset($httpData["category"])) {
                    $data["media_category"] = $httpData["category"];
                }
                $data["media_thumb_url"] = Utility::getThumbnail($data["media_url"]);
                $id = $pdo -> insert("media", $data);
                $ids[] = $id;
            }
        }
        echo json_encode($ids);
    } 

    function createThumb($filePath) {

        $dest = $GLOBALS['utility'] -> getThumbName($filePath);
        
        $content = file_get_contents($filePath);
        file_put_contents($dest, $content);

        // if(file_exists($filePath)) {
        //     if ($ext = $GLOBALS['utility']->getExt($filePath)) {
        //         $w = 300;
        //         $h = 300;
        //         list($width, $height) = getimagesize($filePath);
        //         $r = $width / $height;
        //         if ($w/$h > $r) {
        //             $newwidth = $h*$r;
        //             $newheight = $h;
        //         } else {
        //             $newheight = $w/$r;
        //             $newwidth = $w;
        //         }
        //         $srcResource = getImageResource($filePath, $ext);
        //         $dstResource = imagecreatetruecolor($newwidth, $newheight);
        //         imagecopyresampled($dstResource, $srcResource, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        //         saveImageToFile($dstResource, $GLOBALS['utility'] -> getThumbName($filePath), $ext);
        //     } 
        // }
    }

    function getImageResource($filePath, $ext) {
        switch($ext) {
            case "png":
                return imagecreatefrompng($filePath);
            case "jpg":
            case "jpeg":
                return imagecreatefromjpeg($filePath);
        }
    }

    function saveImageToFile($dstResource, $destPath, $ext) {
        switch($ext) {
            case "png":
                $black = imagecolorallocate($dstResource, 0, 0, 0);
                imagecolortransparent($dstResource, $black);
                return imagepng($dstResource, $destPath);
            case "jpg":
            case "jpeg":
                return imagejpeg($dstResource, $destPath);
        }
    }

    function buildFilePath($path) {
        return constant("RESOURCES_PATH") . "/" . $path;
    }
?>