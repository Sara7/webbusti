<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    $action = $_GET["action"];
    $category_code = $_GET["category_code"];
    
    $pdo = new SQLPdo($db);
    switch($action) {
        case "list":
            if($_GET["level"] != null && $_GET["level"] == 0) {
                $categories = $pdo -> select("category", ["category_parent" => null], ["category_code" => "ASC"]);
            } else if($_GET["category_code"] != null) {
                $categories = $pdo -> select("category", ["category_code*" => $category_code], ["category_parent" => "ASC"]);
            } else {
                $categories = $pdo -> select("category", [], ["category_parent" => "ASC"]);   
            }
            // $structured_categories = [];
            // foreach($categories as $cat) {
            //     if($cat["category_parent"] == null) {
            //         if(!array_key_exists($cat["category_code"], $structured_categories)) {
            //             $structured_categories[$cat["category_code"]] = [];
            //             $structured_categories[$cat["category_code"]]["data"] = $cat;
            //             $structured_categories[$cat["category_code"]]["childs"] = [];
            //         }
            //     } else {
            //         $parent = $cat["category_parent"];
        
            //         if($structured_categories[$parent] != null && !array_key_exists($cat["category_code"], $structured_categories[$parent])) {
            //             $structured_categories[$parent]["childs"][$cat["category_code"]] = [];
            //             $structured_categories[$parent]["childs"][$cat["category_code"]]["data"] = $cat;
            //             $structured_categories[$parent]["childs"][$cat["category_code"]]["childs"] = [];
            //         }
            //     }
            // }
            break;
    }
    echo json_encode($categories);

?>