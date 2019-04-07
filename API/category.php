<?php

    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("./init.php");
    $action = $_GET["action"];
    $category_code = $httpData["category_code"];
    
    $pdo = new SQLPdo($db);
    switch($action) {
        case "get":
            if($httpData["category_code"] != null) {
                $structured_categories = $pdo -> select("category", ["category_code" => $httpData["category_code"]], null)[0];
            }
            break;

        case "list":

            $structured_categories = [];
            if($_GET["category_code"] == "0") {
                $structured_categories = $pdo -> select("category", ["category_level" => 1], ["category_order"=>"ASC", "category_parent" => "ASC"]);
            } else if(isset($_GET["category_code"]) && isset($_GET["for_website"])) {
                $res = $pdo -> select("category", ["category_code*" => $_GET["category_code"]], ["category_order"=>"ASC", "category_parent" => "ASC"]);
                foreach($res as $cat) {
                    if ($cat["category_code"] != $_GET["category_code"]) {
                        $media_id = $pdo -> select("media_per_entity", ["media_per_entity_category" => $cat["category_id"], "media_per_entity_role" => 4])[0]["media_per_entity_media"];
                        $media = $pdo -> select("media", ["media_id" => $media_id])[0];
                        $cat["category_logo"] = $media;
                        $cat_id = $cat["category_id"];
                        $query = "select distinct product.*, media.* from product left join media_per_entity on product.product_id = media_per_entity.media_per_entity_product left join media on media_per_entity_media = media.media_id where media_per_entity_role = 3 and product.product_category = $cat_id";
    
                        $products = $pdo -> customQuery($query);
                        $cat["products"] = $products;
                        $structured_categories[] = $cat;
                    }
                }
            } else {
                $where_category = ["category_code*" => $_GET["category_code"], "category_level" => 1];
                $categories = $pdo -> select("category", $where_category, ["category_order" => "ASC", "category_parent" => "ASC"]);
                
                foreach($categories as $category) {
                    $category["childs"] = [];
                        $structured_categories[$category["category_code"]] = $category;
                    
                }

                $where_category["category_level"] = 2;
                $subcategories = $pdo -> select("category", $where_category, ["category_parent" => "ASC"]);
                foreach($subcategories as $subcategory) {
                    $subcategory["childs"] = [];
                    $structured_categories[$subcategory["category_parent"]]["childs"][$subcategory["category_code"]] = $subcategory;
                }
    
                $where_category["category_level"] = 3;
                $subsubcategories = $pdo -> select("category", $where_category, ["category_parent" => "ASC"]);
                foreach($subsubcategories as $subsubcategory) {
                    $subsubcategory["childs"] = [];
                    $category_root = explode("_", $subsubcategory["category_code"])[0];
                    $structured_categories[$category_root]["childs"][$subsubcategory["category_parent"]]["childs"][$subsubcategory["category_code"]] = $subsubcategory;
                }
                
                $levels = explode("_", $_GET["category_code"]);
                if(sizeof($levels) == 1) {
                    $structured_categories = $structured_categories[$_GET["category_code"]]["childs"];
                } else {
                    $structured_categories = $structured_categories[$levels[0]]["childs"][$_GET["category_code"]]["childs"];//[$levels[1]];
                }
            }
            if(isset($httpData["countProducts"])) {
                foreach($structured_categories as $k => &$v) {
                    if(isset($v["childs"])) {
                        foreach($v["childs"] as $k1 => &$v1) {
                            $result = $pdo->select("product", ["product_category" => $v1["category_id"]]);
                            $v1["products_count"] = sizeOf($result);
                        }
                    }
                }
            }
            break;
    }
    echo json_encode($structured_categories);

?>