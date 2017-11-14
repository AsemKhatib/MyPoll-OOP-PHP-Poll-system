<?php

use MyPoll\Classes\AdminIndex;
use MyPoll\Classes\Database\DBInterface;
use MyPoll\Classes\Database\PDODB;
use MyPoll\Classes\Database\RedBeanDB;
use MyPoll\Classes\Login\RememberMe;
use MyPoll\Classes\Login\Login;
use MyPoll\Classes\Pagination;
use MyPoll\Classes\Components\Questions;
use MyPoll\Classes\Components\Settings;
use MyPoll\Classes\Components\Users;
use MyPoll\Classes\Components\Answers;
use function DI\get;
use function DI\object;

$db_dsn = 'mysql:host=localhost;dbname=mypoll;charset=utf8';
$db_user = 'root';
$db_pass = 'root';
$db_options = [
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

$systemAbsolutePath = __DIR__ . '/../';
$templatePathDirArray = [
    $systemAbsolutePath . '/template/',
    $systemAbsolutePath . '/admin/template/'];
$settingsId = 1;

return [
    'routerArray' => [
        'questions' => get(Questions::class),
        'users' => get(Users::class),
        'answers' => get(Answers::class),
        'settings' => get(Settings::class)
    ],
    'templatePathDirArray' => $templatePathDirArray,
    'settingsId' => $settingsId,
    'db_dsn' => $db_dsn,
    'db_user' => $db_user,
    'db_pass' => $db_pass,
    'db_options' => $db_options,
    'db_driver' => get(PDODB::class),

    DBInterface::class => get(DBInterface::class),

    RedBeanDB::class => object()->constructor(
        get('db_dsn'),
        get('db_user'),
        get('db_pass')
    )->method('setup'),

    PDODB::class => object()->constructor(
        get('db_dsn'),
        get('db_user'),
        get('db_pass'),
        get('db_options')
    )->method('setup'),

    RememberMe::class => object()->constructor(get('db_driver')),

    Twig_Loader_Filesystem::class => object()->constructor(get('templatePathDirArray')),
    Twig_Environment::class => object()->constructor(get(Twig_Loader_Filesystem::class), []),
    Pagination::class => object()->constructor(get('db_driver')),
    Answers::class => object()->constructor(get('db_driver')),

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
        get('db_driver'),
        get(Twig_Environment::class),
        get(Pagination::class),
        get(Settings::class)
    ),

    Questions::class => object()->constructor(
        get('db_driver'),
        get(Answers::class),
        get(Twig_Environment::class),
        get(Pagination::class),
        get(Settings::class)
    ),
    Login::class => object()->constructor(
        get('db_driver'),
        get(RememberMe::class),
        get(Users::class),
        get(Settings::class)
    )
];
