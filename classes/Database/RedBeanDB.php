<?php

namespace MyPoll\Classes\Database;

use phpDocumentor\Reflection\Types\Array_;
use RedBeanPHP\Facade;
use RedBeanPHP\ToolBox;
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
     * @return void
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
        return Facade::count($dbName, $addSQL, $bindings);
    }

    /**
     * @param StoreType $type
     *
     * @return int
     */
    public function store(StoreType $type)
    {
        return $type->storeRedBean();
    }

    /**
     * @param AddColumnType $type
     *
     * @return array|OODBBean
     */
    public function addColumn(AddColumnType $type)
    {
        return $type->addRedBeanColumn();
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
     * @return OODBBean
     */
    public function getById($dbName, $id)
    {
        Facade::load($dbName, $id);
    }

    /**
     * @param mixed $column
     * @param int  $id
     */
    public function delete($column, $id = null)
    {
        Facade::trash($column, $id = null);
    }

    /**
     * @param array $columns
     */
    public function deleteAll($columns)
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
        return Facade::find($type, $sql = null, $bindings = array());
    }
}