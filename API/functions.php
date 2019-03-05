<?php

    //////////////// FEATURES /////////////////////

    function getFeaturesPerProduct($product_id) {
        $query = "select feature.feature_id,
            feature.feature_desc_default,
            feature.feature_type,
            feature.feature_is_mandatory,
            GROUP_CONCAT(feature_per_product.feature_per_product_value SEPARATOR '|') as feature_value
            from feature
            join feature_per_product
            on feature.feature_id = feature_per_product.feature_per_product_feature
            where feature_per_product_product = $product_id
            group by feature.feature_id,
            feature.feature_desc_default,
            feature.feature_type
            order by feature.feature_order ASC
            ";
        $result =  $GLOBALS["pdo"] -> customQuery($query);
        return $result ? $result : [];
    }

    function getFeatureValues($feature_id) {
        $query = "select values_per_feature_id as feature_value_id,
                    values_per_feature_value_name_default as feature_value_desc_default
                    from values_per_feature
                    where values_per_feature_feature = $feature_id";
        $result =  $GLOBALS["pdo"] -> customQuery($query);
        return $result ? $result : [];
    }

    function addFeaturesPerProduct($product_id, $features) {
        if(count($features) > 0) {
            foreach($features as $feature) {
                if($feature["feature_value"]){
                    foreach(explode("|", $feature["feature_value"]) as $val) {
                        $res1 = $GLOBALS["pdo"] -> insert("feature_per_product", ["feature_per_product_feature" => $feature["feature_id"], "feature_per_product_value" => $val, "feature_per_product_product" => $product_id]);
                        if(!$res1) die("feature die");
                    } 
                } else {
                    $res1 = $GLOBALS["pdo"] -> insert("feature_per_product", ["feature_per_product_feature" => $feature["feature_id"], "feature_per_product_value" => "NULL", "feature_per_product_product" => $product_id]);
                    if(!$res1) die("feature die");
                }
            }
        }
    }