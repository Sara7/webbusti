<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("./init.php");
    $pdo = new SQLPdo($db);
    include_once("./functions.php");

    switch($action) {
        case "list":
            if(isset($httpData["category_id"])) {
                if(!is_numeric($httpData["category_id"])) {
                    $res = $pdo->select("category", ["category_code" => $httpData["category_id"]])[0];
                    $category_id  = $res["category_id"];
                } else {
                    $category_id = $httpData["category_id"];
                }
                if(isset($_GET["restrict"])) {
                    //$result = $pdo -> select("feature_per_category", ["feature_per_category_category" => $httpData["category_code"]], ["feature_order" => "ASC", "feature_per_category_feature" => "ASC"]);    
                    $result = $pdo -> selectJoin(["feature", "feature_per_category"], [["feature_id", "feature_per_category_feature"]], ["feature_per_category_category" => $httpData["category_id"]], ["feature_order" => "ASC", "feature_per_category_feature" => "ASC"]);    
                } else {
                    $category = $pdo -> select("category", ["category_id" => $category_id]);
                    $category = $category[0];
                    $levels = explode("_", $category["category_code"]);
                    if(sizeOf($levels) > 0) {
                        $level = $levels[0];
                    }
                    $merged_result = [];
                    $rr = $pdo -> select("category", ["category_code*" => $level]);
                    foreach($rr as $r) {
                        $cat_id = $r["category_id"];
                        $temp = $pdo -> selectJoin(["feature", "feature_per_category"], [["feature_id", "feature_per_category_feature"]], ["feature_per_category_category" => $cat_id, "feature_per_category_is_active"=>1], ["feature_order" => "ASC", "feature_per_category_feature" => "ASC"]);
                        if($temp) {
                            $merged_result = array_merge($merged_result, $temp);
                        }
                    }
                    $result = $merged_result;
                }
            } else if(isset($httpData["product_code"])) {
                $result = $pdo -> select("feature_per_product", ["feature_per_product_product" => $httpData["product_code"]], ["feature_per_product_feature" => "ASC"]);
            } 
            foreach($result as &$feature) {
                $feature_detail = $pdo -> select("feature", ["feature_id" => $feature["feature_per_category_feature"]])[0];
                $feature_values = getFeatureValues($feature["feature_per_category_feature"]);
                foreach($feature_values as &$value) {
                    $value["num_prod"] = getProductsCountPerFeatureValue($value["feature_value_id"]);
                }
                $feature["feature_values"] = $feature_values;
                $feature["feature_value"] = null;
                $feature["num_prod"] = getProductsCountPerFeature($feature["feature_id"]);
            }
            break;

        case "addFeaturesPerProduct": 
            $product_id = $httpData["product_id"];
            $featurePerProduct = json_decode($httpData["featuresPerProduct"], true);
            foreach($featurePerProduct as $featureKey => $featureValues) {
                foreach($featureValues as $featureValue) {
                    $result = $pdo -> insert("feature_per_product", ["feature_per_product_product" => $product_id, "feature_per_product_feature" => $featureKey, "feature_per_product_value" => $featureValue]);
                }
            }
            break;
            
        case "get":
            $result = $pdo->select("feature", ["feature_id" => $httpData["feature_id"]]);
            $result = $result[0];
            $values = $pdo->select("values_per_feature", ["values_per_feature_feature" => $result["feature_id"]]);
            $result["feature_values"] = $values;
            $result["feature_value"] = null;
            break;
        

    }

    echo json_encode($result);

    function getProductsCountPerFeature($feature_id){
        $result = $GLOBALS["pdo"]->customQuery("select count(*) num_prod from feature_per_product where feature_per_product_feature=$feature_id");
        return $result ? $result[0]["num_prod"] : 0;
    }
    function getProductsCountPerFeatureValue($feature_value_id) {
        $result = $GLOBALS["pdo"]->customQuery("select count(*) num_prod from feature_per_product join product on product.product_id = feature_per_product.feature_per_product_product where feature_per_product_value=$feature_value_id and (product_visibility & 2) > 0");
        return $result ? $result[0]["num_prod"] : 0;
    }
?>