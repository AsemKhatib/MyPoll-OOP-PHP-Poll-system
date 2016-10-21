<?php

use MyPoll\Classes\General;
use MyPoll\Classes\AdminIndex;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/Bootstrap.php';

session_start();

$doAction = isset($_GET['do']) ? General::cleanInput('string', $_GET['do']) : null;

/** @var AdminIndex $adminIndex */
$adminIndex = $container->get(AdminIndex::class);

if (empty($doAction) && !method_exists($adminIndex, $doAction)) {
    $adminIndex->defaultAction();
} else {
    $adminIndex->$doAction();
}
