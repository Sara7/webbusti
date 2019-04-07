<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("./init.php");
    include_once("./../utilities/Utility.php");
    $pdo = new SQLPdo($db);
    include_once("./functions.php");

    switch($action) {
        case "get":
            if($httpData["product_id"] != null) {
                $product_id = $httpData["product_id"];

                $result = $pdo -> select("product", ["product_id" => $product_id]);
                $result = $result ? $result[0] : die("No product found for id $product_id");
                $result["product_desc_default"] = base64_decode($result["product_desc_default"]);

                $availability       = getAvailability($product_id);
                $media              = getMediaPerProduct($product_id);
                $paired_products    = getPairedProducts($product_id);
                $category           = getCategoryById($result["product_category"]);
                $features           = getFeaturesPerProduct($product_id);

                foreach($features as &$feature) {
                    $feature_values = getFeatureValues($feature["feature_id"]);
                    $feature["feature_values"] = $feature_values;
                }

                $result["availability"]     = $availability;
                $result["product_media"]    = $media;
                $result["paired_products"]  = $paired_products;
                $result["features"]         = $features;
                $result["product_category"] = $category;

                if($httpData["for_website"]) {
                    for($i = 0; $i < count($media); $i++) {
                        if($media[$i]["media_role"] != 4) {
                            array_splice($media, $i, 1);
                        }
                    }
                    $result["product_media"]    = $media;

                    $i = 0;
                    foreach($features as &$feature) {
                        if(!in_array($feature["feature_id"], ["12", "35", "14", "23"])) {
                            unset($features[$i]);
                        } else {
                            $valName = $pdo -> select("values_per_feature", ["values_per_feature_id" => $feature["feature_value"]])[0]["values_per_feature_value_name_default"];
                            $feature["feature_val_name"] = $valName;
                        }
                        $i++;
                    }
                }
                $result["features"] = $features;

            } else {
                die("No product id");
            }
            break;

        case "list":
            $where = [];
            if(isset($httpData["product_category"])) {
                $product_category = $httpData["product_category"];
                if(!is_numeric($product_category)) {
                    $categories = $pdo -> select("category", ["category_code*" => $product_category]);
                    $categories_ids = [];
                    $categories_string = "";
                    $result = [];
                    foreach($categories as $category) {
                        $result = array_merge($result, $pdo -> select("product", ["product_category" => $category["category_id"]], ["product_id" => "DESC"]));
                    }
                } else {
                    $result = $pdo -> select("product", ["product_id" => $product_category], ["product_id" => "DESC", "product_category" => "ASC"]);
                }
            } else {
                $result = $pdo -> select("product", $where, ["product_id" => "DESC", "product_category" => "ASC"]);
            }
            
            
            foreach ($result as &$record) {
                $category           = getCategoryById($record["product_category"]);
                $media_per_product  = getMediaPerProduct($record["product_id"]);
                $record["product_category"] = $category;
                $record["product_media"] = $media_per_product;
                $record["product_desc_default"] = base64_decode($record["product_desc_default"]);
                $record["features"] = getFeaturesPerProduct($record["product_id"]);

                $canAdd = true;
                if(isset($httpData["featured"])){
                    if(!getFeaturedProducts($product["product_id"])) {
                        $canAdd = false;
                    }
                }
                if(isset($httpData["media_role"])) {
                    if($media_array) {
                        foreach($media_array as $media_arr) {
                            if(!$media_arr["media_role"] == $httpData["media_role"]) {
                            }
                        }
                    }
                }
            }

            if($httpData["hashed"] != null) {
                $structured_products = [];
                foreach($result as $res) {
                    $category_code = $res["product_category"]["category_code"];
                    $product_category_root = explode("_", $category_code)[0];

                    if(!array_key_exists($product_category_root, $structured_products)) {
                        $structured_products[$product_category_root] = [];
                    }
                    $structured_products[$product_category_root][] = $res;   
                }
                $result = $structured_products;
            }
            break;

        case "add":
            if($httpData["product"]) {
                $product = json_decode($httpData["product"], true);
                $product["product_desc_default"] = base64_encode($product["product_desc_default"]);
                $media = null;
                $features = $product["features"];
                $availability = $product["availability"];
                unset($product["features"]);
                unset($product["availability"]);



                if (isset($product["product_media"]) && $product["product_media"] != null) {
                    $media = $product["product_media"];
                    $product["product_media"] = null;
                } 
                $result = $pdo -> insert("product", $product);
                if(!$result) die("INSERT");
                $product_id = $result;
                if($media) {
                    $result3 = $pdo -> insert("media_per_entity", ["media_per_entity_media" => $media["media_id"], "media_per_entity_role" => 3, "media_per_entity_product" => $result]);
                }

                addAvailability($product_id, $availability);
                addFeaturesPerProduct($product_id, $features);
                
            } else {
                $result = $pdo -> insert("product", $httpData["product"]);
                $result = "false";
            }
            break;

        case "edit":
            if($httpData["product"]) {
                $product = json_decode($httpData["product"], true);
                $product["product_desc_default"] = base64_encode($product["product_desc_default"]);
                $product_id = $product["product_id"];
                unset($product["product_id"]);
                $features = $product["features"];
                unset($product["features"]);
                $media = $product["product_media"];
                unset($product["product_media"]);
                $paired_products = $product["paired_products"];
                unset($product["paired_products"]);
                $category = $product["product_category"];
                unset($product["product_category"]);
                $availability = $product["availability"];
                unset($product["availability"]);

                $result = $pdo -> update("product", $product, ["product_id" => $product_id]);
                $media = null;
               
                if($media) {
                    $result2 = $pdo -> delete("media_per_entity", ["media_per_entity_role" => 3, "media_per_entity_product" => $product_id]);
                    $result3 = $pdo -> insert("media_per_entity", ["media_per_entity_media" => $media["media_id"], "media_per_entity_role" => 3, "media_per_entity_product" => $result]);
                }
                
                $pdo->delete("feature_per_product", ["feature_per_product_product" => $product_id]);
                addFeaturesPerProduct($product_id, $features);
                editAvailability($product_id, $availability);
                $result = true;
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
            $is_featured = $httpData["is_featured"];
            if($is_featured) {
                $result = $pdo -> insert("featured_product", ["featured_product_product_id" => $product_id]);
            } else {
                $result = $pdo -> delete("featured_product", ["featured_product_product_id" => $product_id]);
            }
            break;

        case "addPairings": 
            $product_id = $httpData["product_id"];
            $pairings = $httpData["pairings"];
            addPairings($product_id, $pairings);
            break;
        
        case "editPairings":
            $product_id = $httpData["product_id"];
            $pairings = $httpData["pairings"];
            $result = $pdo->delete("pairing", ["pairing_product_id_1" => $product_id]);
            $result2 = $pdo->delete("pairing", ["pairing_product_id_2" => $product_id]);
            addPairings($product_id, $pairings);
            break;

        case "remove":
            $product_id = $httpData["product_id"];
            $result = $pdo-> delete("product", ["product_id" => $product_id]);
            break;

        case "addAvailability":
            $product_id = $httpData["product_id"] * 1;
            $availability = $httpData["availability"];
            if(sizeOf($availability) == 12) {
                $availability_string = implode("|", $availability);
            } 
            $result = $pdo->insert("product_availability", ["product_availability_product_id" => $product_id, "product_availability_value" => $availability_string]);
            break;

        case "editAvailability":
            $product_id = $httpData["product_id"] * 1;
            $availability = $httpData["availability"];
            if(sizeOf($availability) == 12) {
                $availability_string = implode("|", $availability);
            } 
            $result = $pdo->delete("product_availability", ["product_availability_product_id" => $product_id]);
            $result = $pdo->insert("product_availability", ["product_availability_product_id" => $product_id, "product_availability_value" => $availability_string]);
            break;
        
       
    }
    echo json_encode($result);

    function getAvailability($product_id) {
        $result = $GLOBALS["pdo"] -> select("product_availability", ["product_availability_product_id" => $product_id]);

        $av_array = [];
        if($result) {
            $result = $result[0];
            $av_array = explode("|", $result["product_availability_value"]);
        }
        return $av_array;
    }

    function getMediaPerProduct($product_id) {
        $result = $GLOBALS["pdo"] -> select("media_per_entity", ["media_per_entity_product" => $product_id]);
        $media = [];
        foreach($result as $row) {
            $mediaInfo = getMediaInfo($row["media_per_entity_media"]);
            $mediaInfo["media_role"] = $row["media_per_entity_role"];
            $media[] = $mediaInfo;
        }
        return $media;
    }

    function  getMediaInfo($media_id) {
        $result = $GLOBALS["pdo"] -> select("media", ["media_id" => $media_id]);
        return $result ? $result[0] : [];
    }

    function getCategoryByCode($category_code) {
        $result = $GLOBALS["pdo"] -> select("category", ["category_code" => $category_code]);
        return $result ? $result[0] : [];
    }

    function addPairings($product_id, $pairings) {
        foreach($pairings as $k=>$v) {
            $result = $GLOBALS["pdo"]->insert("pairing", ["pairing_product_id_1" => $product_id, "pairing_product_id_2" => $v, "pairing_title_default" => $k]);
        }
    }
    function getCategoryById($category_id) {
        $result = $GLOBALS["pdo"] -> select("category", ["category_id" => $category_id]);
        return $result ? $result[0] : [];
    }

    function getProductInfo($product_id) {
        $result = $GLOBALS["pdo"] -> selectJoin(["product", "category"], [["product_category", "category_code"]], ["product_id" => $product_id]);
        if($result) {
            $result = $result[0];
            $result["product_desc_default"] = base64_decode($result["product_desc_default"]);
        }
        return $result;
    }

    function getPairedProducts($product_id) {
        $result  = $GLOBALS["pdo"] -> select("pairing", ["pairing_product_id_1" => $product_id]);
        $result2 = $GLOBALS["pdo"] -> select("pairing", ["pairing_product_id_2" => $product_id]);

        $ids = [];
        $paired_products = [];
        foreach($result as $r) {
            $ids[] = $r["pairing_product_id_2"];
        }
        foreach($result2 as $r) {
            $ids[] = $r["pairing_product_id_1"];
        }
        foreach($ids as $id) {
            $paired_products[] = getProductInfo($id);
        }
        return $paired_products;
    }

    function getFeaturedProducts($product_id) {
        $result =  $GLOBALS["pdo"] -> select ("featured_product", ["featured_product_product_id" => $product_id]);
        return $result ? $result : [];
    }


    function getFeatureInfo($feature_id) {
        $result =  $GLOBALS["pdo"] -> select ("feature", ["feature_id" => $feature_id]);
        return $result ? $result[0] : null;
    }

    function editAvailability($product_id, $availability) {
        if(sizeOf($availability) == 12) {
            $availability_string = implode("|", $availability);
        } 
        $result = $GLOBALS["pdo"]->delete("product_availability", ["product_availability_product_id" => $product_id]);
        $result = $GLOBALS["pdo"]->insert("product_availability", ["product_availability_product_id" => $product_id, "product_availability_value" => $availability_string]);
    }

    function addAvailability($product_id, $availability) {
        $availability_string = implode("|", $availability);
        $result = $GLOBALS["pdo"]->insert("product_availability", ["product_availability_product_id" => $product_id, "product_availability_value" => $availability_string]);
    }

    

    // $result = $pdo -> select("feature_per_category");
    // foreach($result as $row) {
    //     $category = $row["feature_per_category_category"];
    //     $feature = $row["feature_per_category_feature"];
    //     $feature_id = $pdo->select("feature", ["feature_code" => $feature])[0];
    //     $category_id = $pdo->select("category", ["category_code" => $category])[0];
    //     //$pdo -> update("feature_per_category", ["feature_per_category_feature" => $feature_id["feature_id"]], ["feature_per_category_feature" => $feature_id["feature_code"]]);
    //     $pdo -> update("feature_per_category", ["feature_per_category_category" => $category_id["category_id"]], ["feature_per_category_category" => $category_id["category_code"]]);
    // }


//     $result = $pdo -> select("feature_per_product");
//     foreach($result as $row) {
//         $feature = $row["feature_per_product_feature"];
//         $product = $row["feature_per_product_product"];
//         $feature_id = $pdo->select("feature", ["feature_code" => $feature])[0];
//         $product_id = $pdo->select("product", ["product_code" => $product])[0];
// //        $pdo -> update("feature_per_product", ["feature_per_product_feature" => $feature_id["feature_id"]], ["feature_per_product_feature" => $feature_id["feature_code"]]);
//         $pdo -> update("feature_per_product", ["feature_per_product_product" => $product_id["product_id"]], ["feature_per_product_product" => $product_id["product_code"]]);
//     }
?>