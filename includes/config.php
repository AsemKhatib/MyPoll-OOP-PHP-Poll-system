<?php

require __DIR__ . '/../vendor/autoload.php';

$db_dsn = 'mysql:host=localhost;dbname=mypoll;charset=utf8';
$db_user = 'root';
$db_pass = 'root';

$db = new \MyPoll\Classes\RedBeanDB($db_dsn, $db_user, $db_pass);
$templatePathDir = 'template/';
$settingsId = 1;
