<?php

namespace MyPoll\Classes\Database;

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
     * @return void
     */
    public function setup();

    /**
     * @return mixed
     */
    public function getConnection();

    /**
     * @return mixed
     */
    public function getFinder();

}