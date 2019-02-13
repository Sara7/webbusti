<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("./init.php");
    $action = $httpData["action"];
    $pdo = new SQLPdo($db);
    $result = [];

    switch($action) {
        case "add":
           /* TODO */
            break;
        case "list":
            $res = null;
            $res = $pdo->select("material_category");
            echo json_encode($res);
            break;
    }    
?>