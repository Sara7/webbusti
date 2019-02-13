<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("./init.php");
    
    $pdo = new SQLPdo($db);

    switch($action) {
        case "get":
            if($httpData["product_id"] != null) {
                $result = $pdo -> select("product", ["product_id" => $httpData["product_id"]]);
                if($result) $result = $result[0];
            }
            break;
        case "list":
            
            $wheres = [];
            //$wheres["media_type"] = "PROD";
            if(isset($httpData["product_category"])) {
                $wheres["product_category*"] = $httpData["product_category"];
            }
            if(isset($httpData["product_id"])) {
                $wheres["product_id"] = $httpData["product_id"]*1;
            }

            $result = $pdo -> selectJoin(["product", "category", "media_per_entity", "media"], [["product_category", "category_code"], ["media_per_entity_product", "product_id"], ["media_per_entity_media", "media_id"]], $wheres, ["product_category" => "ASC"]);

            if($httpData["hashed"] != null && $httpData["hashed"] == "true") {
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

            if($httpData["category_detail"]) {
                foreach($result as &$product) {
                    $category_code = $product["product_category"];
                    $categor_detail = $pdo -> select("category", ["category_code" => $category_code])[0];
                    $product["category_detail"] = $categor_detail;
                }
            }
            foreach($result as &$product) {
                $product_code = $product["product_code"];
                $product_features = $pdo -> select("feature_per_product", ["feature_per_product_product" => $product_code]);
                $product["product_features"] = $product_features;
            }
            break;
        case "add":
            if($httpData["product"]) {
                $result = $pdo -> insert("product", $httpData["product"]);
            } else {
                $result = false;
            }
            break;
    }
    echo json_encode($result);
?>