<?php

namespace MyPoll\Classes\Database;

use RedBeanPHP\Facade;
use RedBeanPHP\OODBBean;

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
    }

    /**
     * @param string $dbName   type of bean we are looking for
     * @param string $addSQL   additional SQL snippet
     * @param array  $bindings parameters to bind to SQL
     *
     * @return int
     */
    public function count($dbName, $addSQL = '', $bindings = array())
    {
        Facade::count($dbName, $addSQL = '', $bindings = array());
    }

    /**
     * @param string $type
     * @param array $rows
     *
     * @return array
     */
    public function addRows($type, $rows)
    {
        $arrayToSave = array();
        foreach ($rows as $row) {
            $arrayToSave[] = Facade::dispense($type)->import($row);
        }
        return $arrayToSave;
    }

    /**
     * @param array $columns
     *
     * @return integer
     */
    public function store($columns)
    {
        Facade::storeAll($columns);
    }

    /**
     * @param string $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function getAll($sql, $bindings)
    {
        Facade::getAll($sql, $bindings);
    }

    /**
     * @param string $dbName
     * @param int    $id
     *
     * @return array
     */
    public function getById($dbName, $id)
    {
        array(Facade::load($dbName, $id));
    }

    /**
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getRow($sql, $bindings)
    {
        Facade::getRow($sql, $bindings = array());
    }

    /**
     * @param string $table
     * @param int  $id
     *
     * @return void
     */
    public function deleteById($table, $id = null)
    {
        Facade::trash($table, $id = null);
    }

    /**
     * @param array $columns
     *
     * @return void
     */
    public function delete($columns)
    {
        Facade::trashAll($columns);
    }

    /**
     * @param string $type
     * @param string   $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function find($type, $sql = null, $bindings = array())
    {
        Facade::find($type, $sql = null, $bindings = array());
    }

    /**
     * @param string $type
     * @param string   $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function findOne($type, $sql = null, $bindings = array())
    {
        array(Facade::findOne($type, $sql = null, $bindings = array()));
    }
}