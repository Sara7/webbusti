<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    $action = $_GET["action"];
    $pdo = new SQLPdo($db);
    $result = [];

    switch($action) {
        case "list":
            if($_GET["category_code"] != null) {
                $result = $pdo -> select("product", ["product_category*" => $category_code]);
            } else {
                $result = $pdo -> select("product", [], ["product_category" => "ASC", "product_name_default" => "DESC"]);
            }
            break;
    }
    echo json_encode($result);
?>