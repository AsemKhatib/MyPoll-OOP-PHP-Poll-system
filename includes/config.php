<?php

require __DIR__ . '/../vendor/autoload.php';

$db_dsn = 'mysql:host=localhost;dbname=mypoll;charset=utf8';
$db_user = 'root';
$db_pass = 'root';

$db = new \MyPoll\Classes\RedBeanDB($db_dsn, $db_user, $db_pass);
$toolBox = \RedBeanPHP\Facade::getToolBox();
$db->setup(
    array(
        'toolBox' => $toolBox,
        'finder' => new \RedBeanPHP\Finder($toolBox)
    )
);

$templatePathDir = 'template/';
$settingsId = 1;
