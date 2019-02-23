<?php


    //reuirements
    require_once('./OAuth2/Autoloader.php');

    // error reporting (this is a demo, after all!)
    ini_set('display_errors',1);error_reporting(E_ALL);


    OAuth2\Autoloader::register();

    $dsn='mysql:host=89.46.111.53;dbname=Sql1148692_5';
    $username="Sql1148692";
    $password="83j228v3zt";

    $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

    $server = new OAuth2\Server($storage); 
    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
    $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

?>