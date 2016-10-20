<?php

use function DI\object;
use function DI\get;
use MyPoll\Classes\RedBeanDB;
use MyPoll\Classes\Pagination;
use MyPoll\Classes\Users;
use MyPoll\Classes\Login;
use MyPoll\Classes\Settings;
use MyPoll\Classes\Questions;
use MyPoll\Classes\Cookie;

return [
    RedBeanDB::class => object()->constructor($db_dsn, $db_user, $db_pass),
    Twig_Environment::class => object()->constructor($loaderAdmin, array()),
    Pagination::class => object()->constructor(get(RedBeanDB::class)),

    Settings::class => object()->constructor(
        get(Twig_Environment::class),
        $settingsId
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
