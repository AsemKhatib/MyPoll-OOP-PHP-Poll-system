<?php

use MyPoll\Classes\AdminIndex;
use MyPoll\Classes\Database\DBInterface;
use MyPoll\Classes\Database\RedBeanDB;
use MyPoll\Classes\Login\RememberMe;
use MyPoll\Classes\Login\Login;
use MyPoll\Classes\Pagination;
use MyPoll\Classes\Questions;
use MyPoll\Classes\Settings;
use MyPoll\Classes\Users;
use function DI\get;
use function DI\object;

$db_dsn = 'mysql:host=localhost;dbname=mypoll;charset=utf8';
$db_user = 'root';
$db_pass = 'root';

$templatePathDir = 'template/';
$settingsId = 1;

return [
    'routerArray' => array(
        'questions' => get(Questions::class),
        'users' => get(Users::class),
        'settings' => get(Settings::class)
    ),
    'templatePathDir' => $templatePathDir,
    'settingsId' => $settingsId,
    'db_dsn' => $db_dsn,
    'db_user' => $db_user,
    'db_pass' => $db_pass,

    DBInterface::class => get(DBInterface::class),
    RedBeanDB::class => object()->constructor(get('db_dsn'), get('db_user'), get('db_pass'))->method('setup'),
    RememberMe::class => object()->constructor(get(RedBeanDB::class)),

    Twig_Loader_Filesystem::class => object()->constructor(get('templatePathDir')),
    Twig_Environment::class => object()->constructor(get(Twig_Loader_Filesystem::class), array()),
    Pagination::class => object()->constructor(get(RedBeanDB::class)),

    AdminIndex::class => object()->constructor(
        get(Twig_Environment::class),
        get(Settings::class),
        get(Login::class)
    ),

    Settings::class => object()->constructor(
        get(Twig_Environment::class),
        get(RedBeanDB::class),
        get('settingsId')
    ),

    Users::class => object()->constructor(
        get(RedBeanDB::class),
        get(Twig_Environment::class),
        get(Pagination::class),
        get(Settings::class)
    ),

    Questions::class => object()->constructor(
        get(RedBeanDB::class),
        get(Twig_Environment::class),
        get(Pagination::class),
        get(Settings::class)
    ),
    Login::class => object()->constructor(
        get(RedBeanDB::class),
        get(RememberMe::class),
        get(Users::class),
        get(Settings::class)
    )
];
