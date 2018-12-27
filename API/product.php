<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    $action = $_GET["action"];
    $pdo = new SQLPdo($db);
    $result = [];

    switch($action) {
        case "list":
            if($_GET["product_category"] != null) {
                $product_category = $_GET["product_category"];
                $result = $pdo -> select("product", ["product_category*" => $product_category], ["product_category" => "ASC"]);
            } else {
                $result = $pdo -> select("product", [], ["product_category" => "ASC", "product_name_default" => "DESC"]);
            }
            if($_GET["hashed"] != null && $_GET["hashed"] == "true") {
                $structured_products = [];
                foreach($result as $res) {
                    $product_category = $res["product_category"];
                    $product_category_root = explode("_", $product_category)[0];
                    if(!array_key_exists($product_category_root, $structured_products)) {
                        $structured_products[$product_category_root] = [];
                    }
                    $structured_products[$product_category_root][] = $res;   
                }
                $result = $structured_products;
            }
            break;
    }
    echo json_encode($result);
?>