<?php

namespace API;

require_once __DIR__ . "./../vendor/autoload.php";
require_once __DIR__ . "./../DataAccess/Config/Database.php";
require_once __DIR__ . "./../DataAccess/Dao/SQLPdo.php";
require_once __DIR__ . "/tools.php";

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DataAccess\Config;
use DataAccess\Dao;
use Slim;

//$db  = new Config\Database("89.46.111.53", "Sql1148692", "83j228v3zt", "Sql1148692_4");
$db  = new Config\Database("89.46.111.78", "Sql1281597", "07071cc1e5", "Sql1281597_1");
$pdo = new Dao\SQLPdo($db->getPdo());

$app = new Slim\App();

require_once("./routes/product.php");

$app->run();