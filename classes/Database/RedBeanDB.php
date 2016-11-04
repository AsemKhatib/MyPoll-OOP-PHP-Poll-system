<?php

namespace MyPoll\Classes\Database;

use RedBeanPHP\Facade;
use RedBeanPHP\ToolBox;

class RedBeanDB implements DBInterface
{
    /** @var string */
    protected $db_dsn;

    /** @var string */
    protected $db_user;

    /** @var string */
    protected $db_pass;

    /** @var array */
    protected $db_options;

    /** @var  ToolBox */
    protected $toolBox;

    /**
     * RedBeanDB constructor.
     *
     * @param string $db_dsn
     * @param string $db_user
     * @param string $db_pass
     * @param string $db_options
     */
    public function __construct($db_dsn, $db_user, $db_pass, $db_options = '')
    {
        $this->db_dsn = $db_dsn;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_options = $db_options;
    }

    /**
     * @return mixed
     */
    public function setup()
    {
        Facade::setup($this->db_dsn, $this->db_user, $this->db_pass);
        $this->toolBox = Facade::getToolBox();
    }

    /**
     * @return ToolBox
     */
    public function getConnection()
    {
        return $this->toolBox;
    }

    /**
     * @param string $dbName
     *
     * @return int
     */
    public function count($dbName)
    {
        return Facade::count($dbName);
    }

    /**
     * @param array|string $stringOrArray
     *
     * @return array|OODBBean
     */
    public function addColumn($stringOrArray)
    {
        return Facade::dispense($stringOrArray);
    }

}