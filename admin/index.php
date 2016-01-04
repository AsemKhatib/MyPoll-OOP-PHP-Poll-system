<?php

use MyPoll\Classes\AdminIndex;
use MyPoll\Classes\General;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/config.php';

// Start the session
session_start();

$twigAdmin->addGlobal('session', $_SESSION);
$do = isset($_GET['do']) ? General::cleanInput('string', $_GET['do']) : null;

$adminIndex = new AdminIndex($twigAdmin);

if (!empty($do) && method_exists($adminIndex, $do)) {
    $adminIndex->$do();
} else {
    $adminIndex->defaultAction();
}
