<?php

use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$db_dsn = 'mysql:host=localhost;dbname=mypoll;charset=utf8';
$db_user = 'root';
$db_pass = 'root';

$templatePathDir = 'template/';
$settingsId = 1;
$loaderAdmin = new Twig_Loader_Filesystem($templatePathDir);

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/DIConfig.php');
$container = $containerBuilder->build();
