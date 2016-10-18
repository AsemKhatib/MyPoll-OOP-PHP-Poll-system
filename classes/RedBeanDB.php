<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;
use RedBeanPHP\Finder;
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

    /** @var  Finder */
    protected $finder;

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
     * @param array $array
     *
     * @return mixed
     */
    public function setup($array = [])
    {
        Facade::setup($this->db_dsn, $this->db_user, $this->db_pass);
        $this->toolBox = $array['toolBox'];
        $this->finder = $array['finder'];
    }

    /**
     * @return ToolBox
     */
    public function getConnection()
    {
        return $this->toolBox;
    }

    /**
     * @return Finder
     */
    public function getFinder()
    {
        return $this->finder;
    }

}