<?php

namespace MyPoll\Classes;

interface DBInterface
{
    /**
     * DBInterface constructor.
     *
     * @param string $db_dsn
     * @param string $db_user
     * @param string $db_pass
     * @param mixed $db_options
     */
    public function __construct($db_dsn, $db_user, $db_pass, $db_options = '');

    /**
     * @param array $array
     *
     * @return mixed
     */
    public function setup($array = []);

    public function getConnection();
}