<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("./init.php");
    include_once("./../utilities/Utility.php");

    require_once("../auth/resource_test.php");

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
            if(isset($httpData["product_category"])) {
                $wheres["product_category*"] = $httpData["product_category"];
            }
            if(isset($httpData["product_id"])) {
                $wheres["product_id"] = $httpData["product_id"]*1;
            }
            

            $parsedResult = [];
            //$result = $pdo -> selectJoin(["product", "category", "media_per_entity", "media"], [["product_category", "category_code"], ["media_per_entity_product", "product_id"], ["media_per_entity_media", "media_id"]], $wheres, ["product_id" => "DESC", "product_category" => "ASC"]);
            $result = $pdo -> selectJoin(["product", "category"], [["product_category", "category_code"]], $wheres, ["product_id" => "DESC", "product_category" => "ASC"]);

            foreach ($result as $record) {
                $product = [];
                $category = [];
                $media = [];
                foreach( $record as $k => $v) {
                    switch($pdo -> getTableNameFromField($k)) {
                        case "category":
                            $category[$k] = $v;
                            break;
                        case "media":
                            $media[$k] = $v;
                            break;
                        case "product":
                            $product[$k] = $v;
                            break;
                    }
                }
                $product["category"] = $category;


                $canAdd = true;
                $featuredIds =  $pdo -> select ("featured_product", ["featured_product_product_id" => $product["product_id"]]);
                if(sizeOf($featuredIds) > 0) {
                    $product["product_is_featured"] = true;
                } else {
                    if(isset($httpData["featured"])) {
                        $canAdd = false;
                    }
                }
                if(isset($httpData["featured"])) {
                    $canAdd = true;
                }
                if($canAdd) {
                    if(isset($httpData["media_role"])) {
                        $media = $pdo -> selectJoin(["media_per_entity", "media"], [["media_per_entity_media", "media_id"]], ["media_per_entity_product" => $product["product_id"], "media_per_entity_role" => $httpData["media_role"]]);
                    }
                    
                    if($media) {
                        $product["media"] = $media;
                    }
                    $parsedResult[] = $product;
                }
            }

            $result = $parsedResult;

            if($httpData["hashed"] != null) {
                $structured_products = [];
                foreach($result as $res) {
                    $product_category = $res["product_category"];
                    $product_category_root = explode("_", $product_category)[0];
                    if(!array_key_exists($product_category_root, $structured_products)) {
                        $structured_products[$product_category_root] = [];
                    }
                    $structured_products[$product_category_root][] = $res;   
                }
                if($httpData["hashed"] == "tdrue") {
                    $result = $structured_products;
                } else if($httpData["hashed"] == "perCategory") {
                    $result = [];
                    foreach($structured_products as $k=>$v) {
                        $cat = $pdo->select("category", ["category_code" => $k])[0];
                        $result[] = [
                            "category" => $cat,
                            "products" => $v
                        ];
                    }
                }
                
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
                $product = json_decode($httpData["product"], true);
                $media = null;
                if (isset($product["product_media"]) && $product["product_media"] != null) {
                    $media = $product["product_media"];
                    $product["product_media"] = null;
                } 
                $result = $pdo -> insert("product", $product);
                if($media) {
                    $result3 = $pdo -> insert("media_per_entity", ["media_per_entity_media" => $media["media_id"], "media_per_entity_role" => 3, "media_per_entity_product" => $result]);
                }
                
            } else {
                $result = $pdo -> insert("product", $httpData["product"]);
                $result = false;
            }
            break;

        case "codeList":
            $codes = [];
            $result = $pdo -> select("product", null, ["product_code" => 'ASC']);
            foreach($result as $record) {
                $codes[$record["product_code"]] = 0;
            }
            $result = $codes;
            break;

        case "addPerProduct":
            $media_id=$httpData["media_id"];
            $product_id=$httpData["product_id"];
            $role=$httpData["role"];
            $result = $pdo -> insert("media_per_entity", ["media_per_entity_media" => $media_id, "media_per_entity_role" => $role, "media_per_entity_product" => $product_id]);
            break;
        case "setFeatured":

            $product_id = $httpData["product_id"];
            $result = $pdo -> insert("featured_product", ["featured_product_product_id" => $product_id]);
            break;
        case "addPairings": 
            $product_id = $httpData["product_id"];
            $pairings = $httpData["pairings"];
            foreach($pairings as $k=>$v) {
                $result = $pdo->insert("pairing", ["pairing_product_id_1" => $product_id, "pairing_product_id_2" => $v, "pairing_title_default" => $k]);
            }
            break;
        case "remove":
            $product_id = $httpData["product_id"];
            $result = $pdo-> delete("product", ["product_id" => $product_id]);
            break;
        case "addAvailability":
            $product_id = $httpData["product_id"];
            $availability = $httpData["availability"];
            print_r($availability);
            die();
            break;
       
    }
    echo json_encode($result);
?>