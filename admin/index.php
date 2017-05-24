<?php

use MyPoll\Classes\AdminIndex;
use MyPoll\Classes\General;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/Bootstrap.php';

session_start();

$doAction = isset($_GET['do']) ? General::cleanInput('string', $_GET['do']) : null;
$router = isset($_GET['route']) ? General::cleanInput('string', $_GET['route']) : null;

$adminIndex = $container->get(AdminIndex::class);

//TODO : fix the code here because it's not beautiful nor cool

if (!method_exists($adminIndex, $doAction)) {
    $adminIndex->defaultAction();
} else {
    if (array_key_exists($router, $container->get('routerArray'))) {
        $adminIndex->$doAction($container->get('routerArray')[$router]);
    } else {
        $adminIndex->$doAction();
    }
}
