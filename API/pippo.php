<?php
    include_once("../DataAccess/Config/init.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    include_once("./init.php");
    
    $pdo = new SQLPdo($db);

    echo json_encode($pdo->selectJoin(["product", "category"], [["product_category", "category_code"]], ["media_p"], null));
?>