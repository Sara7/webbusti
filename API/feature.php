<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("./init.php");
    $pdo = new SQLPdo($db);
    switch($action) {
        case "list":
            if(isset($httpData["category_code"])) {
                if(isset($_GET["restrict"])) {
                    $result = $pdo -> select("feature_per_category", ["feature_per_category_category" => $httpData["category_code"]], ["feature_per_category_feature" => "ASC"]);    
                } else {
                    $levels = explode("_", $httpData["category_code"]);
                    $result = [];
                    foreach($levels as $level) {
                        $result = array_merge($result, $pdo -> select("feature_per_category", ["feature_per_category_category*" => $level], ["feature_per_category_feature" => "ASC"]));
                    }
                }
            } else if(isset($httpData["product_code"])) {
                $result = $pdo -> select("feature_per_product", ["feature_per_product_product" => $httpData["product_code"]], ["feature_per_product_feature" => "ASC"]);
            }
            foreach($result as &$feature) {
                $feature_detail = $pdo -> select("feature", ["feature_code" => $feature["feature_per_category_feature"]])[0];
                $feature_values = $pdo -> select("values_per_feature", ["values_per_feature_feature" => $feature["feature_per_category_feature"]]);
                $feature_detail["feature_values"] = $feature_values;
                $feature["feature_detail"] = $feature_detail;
            }
            
            break;

        case "addFeaturesPerProduct": 
            $featurePerProduct = json_decode($httpData["featuresPerProduct"], true);
            foreach($featurePerProduct as $feature) {
                try {
                    $result = $pdo -> insert("feature_per_product", $feature);
                } catch (Exception $e) {
                    $result = $pdo -> update("feature_per_product", ["feature_per_product_value" => $feature["feature_per_product_value"]], ["feature_per_product_product" => $feature["feature_per_product_product"], "feature_per_product_feature" => $feature["feature_per_product_feature"]]);
                }
            }
            
            break;
        }
    echo json_encode($result);
?>