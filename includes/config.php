<?php

use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';


$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/DIConfig.php');
$container = $containerBuilder->build();
