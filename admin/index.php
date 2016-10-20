<?php

use MyPoll\Classes\General;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/config.php';

session_start();

$doAction = isset($_GET['do']) ? General::cleanInput('string', $_GET['do']) : null;

/** @var \MyPoll\Classes\AdminIndex $adminIndex */
$adminIndex = $container->get('AdminIndex');

if (empty($doAction) && !method_exists($adminIndex, $doAction)) {
    $adminIndex->defaultAction();
} else {
    $adminIndex->$doAction();
}
