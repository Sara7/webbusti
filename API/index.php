<?php

    include_once("../DataAccess/Config/Database.php");
    include_once("../DataAccess/Dao/SQLPdo.php");
    $db = new Database("localhost", "root", "mysql", "bustistore");
    $pdo = new SQLPdo($db);

    echo json_encode($pdo->select("product", ["product_id"=>$_GET["id"]]));
?>