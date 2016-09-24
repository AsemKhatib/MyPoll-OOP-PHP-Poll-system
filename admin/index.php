<?php

use MyPoll\Classes\AdminIndex;
use MyPoll\Classes\General;
use MyPoll\Classes\Factory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/config.php';

// Start the session
session_start();

$do = isset($_GET['do']) ? General::cleanInput('string', $_GET['do']) : null;

$systemFactory = new Factory();
$adminIndex = new AdminIndex($systemFactory);

if (!empty($do) && method_exists($adminIndex, $do)) {
    $adminIndex->$do();
} else {
    $adminIndex->defaultAction();
}
