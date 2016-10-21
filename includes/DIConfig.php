<?php

use function DI\get;
use function DI\object;
use MyPoll\Classes\AdminIndex;
use MyPoll\Classes\Cookie;
use MyPoll\Classes\Login;
use MyPoll\Classes\Pagination;
use MyPoll\Classes\Questions;
use MyPoll\Classes\RedBeanDB;
use MyPoll\Classes\Settings;
use MyPoll\Classes\Users;

$db_dsn = 'mysql:host=localhost;dbname=mypoll;charset=utf8';
$db_user = 'root';
$db_pass = 'root';

$templatePathDir = 'template/';
$settingsId = 1;

return [
    'templatePathDir' => $templatePathDir,
    'settingsId' => $settingsId,
    'db_dsn' => $db_dsn,
    'db_user' => $db_user,
    'db_pass' => $db_pass,

    RedBeanDB::class => object()->constructor(get('db_dsn'), get('db_user'), get('db_pass'))->method('setup'),

    Twig_Loader_Filesystem::class => object()->constructor(get('templatePathDir')),
    Twig_Environment::class => object()->constructor(get(Twig_Loader_Filesystem::class), array()),
    Pagination::class => object()->constructor(get(RedBeanDB::class)),

    AdminIndex::class => object()->constructor(
        get(Twig_Environment::class),
        get(Settings::class),
        get(Login::class),
        get(Questions::class),
        get(Users::class)
    ),

    Settings::class => object()->constructor(
        get(Twig_Environment::class),
        get(RedBeanDB::class),
        get('settingsId')
    ),

    Users::class => object()->constructor(
        get(Twig_Environment::class),
        get(Pagination::class),
        get(Settings::class)
    ),

    Questions::class => object()->constructor(
        get(Twig_Environment::class),
        get(Pagination::class),
        get(Settings::class)
    ),
    Login::class => object()->constructor(
        get(Users::class),
        get(Settings::class)
    ),

    Cookie::class => object()->constructor()
];
