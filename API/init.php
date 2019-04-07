<?php

    use DataAccess\Config\Database;

    $db  = new Database("89.46.111.53", "Sql1148692", "83j228v3zt", "Sql1148692_4");
    

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
            $httpData = $_POST ? $_POST : json_decode(file_get_contents('php://input'), true);
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