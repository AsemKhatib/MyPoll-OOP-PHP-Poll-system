<?php

use MyPoll\Classes\AdminIndex;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/config.php';

// Start the session
session_start();

$twigAdmin->addGlobal('session', $_SESSION);
$do = isset($_GET['do']) ? $_GET['do'] : '';

$adminIndex = new AdminIndex($twigAdmin);

if (!empty($do) && method_exists($adminIndex, $do)) {
    $adminIndex->$do();
} else {
    $adminIndex->defaultAction();
}
