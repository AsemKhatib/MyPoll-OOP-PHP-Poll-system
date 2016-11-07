<?php

require_once 'vendor/autoload.php';
$db_dsn = 'mysql:host=localhost;dbname=mypoll;charset=utf8';
$db_user = 'root';
$db_pass = 'root';
RedBeanPHP\Facade::setup($db_dsn, $db_user, $db_pass);

$saveArr = array();

$arr = array(
    array('qid' => 555, 'answer' => 'heeey', 'votes' => 22),
    array('qid' => 666, 'answer' => 'heeeysss', 'votes' => 2332),
);
foreach ($arr as $item) {
    $saveArr[] = \RedBeanPHP\R::dispense('answers')->import($item);
}

$save = \RedBeanPHP\R::storeAll($saveArr);

var_dump($save);
