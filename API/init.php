<?php

    $httpData = null;
    $action = null;
    $data = null;
    if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit;
    }
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        $httpData = $_GET;
    } else if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(strpos($_SERVER["CONTENT_TYPE"], "multipart/form-data") >= 0) {
            $httpData = $_POST;
        } else {
            $httpData = json_decode(file_get_contents('php://input'), true);    
        }
    } 
    if(array_key_exists('action', $httpData)) {
        $action = $httpData["action"];
    }
    if(array_key_exists('data', $httpData)) {
        $data = $httpData["data"];
    }

?>