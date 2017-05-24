<?php

namespace MyPoll\Classes\Database;

use RedBeanPHP\Facade;
use Exception;

/**
 * Class RedBeanDB
 * @package MyPoll\Classes\Database
 */
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
        if (!Facade::testConnection()) {
            Facade::setup($this->db_dsn, $this->db_user, $this->db_pass);
        }
    }

    /**
     * @param string $dbName   type of bean we are looking for
     *
     * @return int
     */
    public function count($dbName)
    {
        return Facade::count($dbName);
    }

    /**
     * @param string $table
     * @param array $rows
     *
     * @return array
     */
    public function addRows($table, $rows)
    {
        $arrayToSave = array_map(function ($row) use ($table) {
            return Facade::dispense($table)->import($row);
        }, $rows);

        return $arrayToSave;
    }

    /**
     * @param array $modelArray
     * @param array $dataArray
     *
     * @return array
     *
     * @throws \Exception
     */
    public function editRow($modelArray, $dataArray)
    {
        $bean = $modelArray[0];
        if (is_array($bean)) {
            throw new Exception('The sub array should not be of type Array');
        }
        return array($bean->import($dataArray));
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    public function store($rows)
    {
        return Facade::storeAll($rows);
    }

    /**
     * @param string $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function getAll($sql, $bindings)
    {
        return Facade::getAll($sql, $bindings);
    }

    /**
     * @param string $table
     * @param int    $id
     * @param string $type
     *
     * @return array
     */
    public function getById($table, $id, $type = null)
    {
        $resultObject = Facade::load($table, $id);
        if ($type == 'bean') {
            $result = ($resultObject->isEmpty()) ? array() : $resultObject;
            return array($result);
        }
        $resultObject = $resultObject->export();
        $result = ($resultObject['id'] == 0) ? array() : $resultObject;
        return $result;
    }

    /**
     * @param string $table
     * @param int  $id
     *
     * @return void
     */
    public function deleteById($table, $id)
    {
        Facade::trash($table, $id);
    }

    /**
     * @param string $table
     * @param array $rows
     *
     * @return void
     */
    public function deleteAll($table, $rows)
    {
        array_map(function ($row) use ($table) {
            Facade::trash($table, $row['id']);
        }, $rows);
    }

    /**
     * Return an Array of Beans or empty array in case of no results
     *
     * @param string $table
     * @param string   $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function find($table, $sql, $bindings = array())
    {
        $returnArray = array();
        $beans = Facade::find($table, $sql, $bindings);
        if (!empty($beans)) {
            $returnArray = array_map(function ($bean) use ($table) {
                return $bean->export();
            }, $beans);
        }
        return $returnArray;
    }

    /**
     * Return the first Bean only or Null in case of no results
     *
     * @param string $table
     * @param string   $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function findOne($table, $sql, $bindings = array())
    {
        $result = Facade::findOne($table, $sql, $bindings);
        if ($result == null) {
            return array();
        }
        return $result->export();
    }

    /**
     * @param array $array
     *
     * @return int
     */
    public function getID($array)
    {
        return (int) $array[0];
    }

    /**
     * @param string  $sql
     * @param array $bindings
     *
     * @return int
     */
    public function exec($sql, $bindings = array())
    {
        return Facade::exec($sql, $bindings);
    }
}
