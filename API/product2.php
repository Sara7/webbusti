<!-- <?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("./init.php");
    include_once("./../utilities/Utility.php");

    $pdo = new SQLPdo($db);
    switch($action) {
        case "get":
            if($httpData["product_id"] != null) {
                $product_id = $httpData["product_id"];

                $result = $pdo -> select("product", ["product_id" => $product_id]);
                $result = $result ? $result[0] : die("No product found for id $product_id");
                $result["product_desc_default"] = base64_decode($result["product_desc_default"]);

                $availability    = getAvailability($product_id);
                $media           = getMediaPerProduct($product_id);
                $paired_products = getPairedProducts($product_id);
                $features        = getFeaturesPerProduct($product_id);
                $features        = getCategoryInfo($product_id);

                $result["availability"]    = $availability;
                $result["media"]           = $media;
                $result["paired_products"] = $paired_products;
                $result["features"]        = $features;
                //$result["category"]        = $featu

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
                        $result = array_merge($result, $pdo -> select("product", ["product_category" => $category["category_id"]]));
                    }
                } else {
                    $result = $pdo -> select("product", $where, ["product_id" => "DESC", "product_category" => "ASC"]);
                }
            }
            
            foreach ($result as &$record) {
                $category           = getCategoryInfo($record["product_category"]);
                $media_per_product  = getMediaPerProduct($record["product_id"]);
                $record["category"] = $category;
                $record["media"] = $media_per_product;
                $record["product_desc_default"] = base64_decode($record["product_desc_default"]);

                $canAdd = true;
                if(isset($httpData["featured"])){
                    if(!getFeaturedProducts($product["product_id"])) {
                        $canAdd = false;
                    }
                }
                if(isset($httpData["media_role"])) {
                    foreach($media_array as $media_arr) {
                        if(!$media_arr["media_role"] == $httpData["media_role"]) {
                        }
                    }
                }
            }
            break;

        case "add":
            if($httpData["product"]) {
                $product = json_decode($httpData["product"], true);
                $product["product_desc_default"] = base64_encode($product["product_desc_default"]);
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
            $product_id = $httpData["product_id"] * 1;
            $availability = $httpData["availability"];
            if(sizeOf($availability) == 12) {
                $availability_string = implode("|", $availability);
            } 
            $result = $pdo->insert("product_availability", ["product_availability_product" => $product_id, "product_availability_value" => $availability_string]);
            break;
       
    }
    //echo json_encode($result);

    function getAvailability($product_id) {
        $result = $GLOBALS["pdo"] -> select("product_availability", ["product_availability_product" => $product_id]);

        $availability = [];
        if($result) {
            $result = $result[0];
            $av_array = explode("|", $result["product_availability_value"]);
            foreach($av_array as $element) {
                switch($element) {
                    case "1":
                        $availability[]="yes";
                        break;
                    case "2":
                        $availability[]="maybe";
                        break;
                    case "3":
                        $availability[]="nope";
                        break;
                    default:
                        $availability[]="yes";
                        break;
                }
            }
        }
        return $availability;
    }

    function getMediaPerProduct($product_id) {
        $result = $GLOBALS["pdo"] -> select("media_per_entity", ["media_per_entity_product" => $product_id]);
        $media = [];
        foreach($result as $row) {
            $media[] = getMediaInfo($row["media_per_entity_media"]);
        }
        return $media;
    }

    function getMediaInfo($media_id) {
        $result = $GLOBALS["pdo"] -> select("media", ["media_id" => $media_id]);
        return $result ? $result[0] : [];
    }

    function getCategoryInfo($category_code) {
        $result = $GLOBALS["pdo"] -> select("category", ["category_code" => $category_code]);
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

    function getFeaturesPerProduct($product_id) {
        $result = $GLOBALS["pdo"] -> select("feature_per_product", ["feature_per_product_product" => $product_id]);
        return $result ? $result : [];
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
?> -->