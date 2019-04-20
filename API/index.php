<?php

namespace API;

require_once __DIR__ . "./../vendor/autoload.php";
require_once __DIR__ . "/tools.php";

// Autoload all classes
spl_autoload_register("classAutoLoad");

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim;

//$db  = new \DB("89.46.111.53", "Sql1148692", "83j228v3zt", "Sql1148692_4");
$conn = new \DB("89.46.111.78", "Sql1281597", "07071cc1e5", "Sql1281597_1");

$app = new Slim\App();

require_once("./routes/product.php");

$app->run();