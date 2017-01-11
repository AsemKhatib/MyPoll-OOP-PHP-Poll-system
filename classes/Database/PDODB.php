<?php

namespace MyPoll\Classes\Database;

use PDO;
use PDOException;
use Exception;

class PDODB implements DBInterface
{
    /** @var  PDO */
    protected $dbi = false;

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
        if (!$this->dbi) {
            try {
                $this->dbi = new PDO($this->db_dsn, $this->db_user, $this->db_pass, $this->db_options);
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
        }
    }

    /**
     * @param string $dbName   type of bean we are looking for
     *
     * @return int
     */
    public function count($dbName)
    {
        return (int) $this->dbi->query('SELECT COUNT(*) FROM ' . $dbName)->fetchColumn();
    }

    /**
     * @param string $table
     * @param array  $rows
     *
     * @return array
     */
    public function addRows($table, $rows)
    {
        return array_walk($rows, array($this, 'addRowsWalk'), $table);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $table
     *
     * @return array
     */
    private function addRowsWalk($key, $value, $table)
    {
        $stmt[] = ('INSERT INTO ' . $table . ' ('. $key .') Values ('. $value .')');
        return $stmt;
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
        $resultsIDs = array();

        foreach ($rows as $statement) {
            $stmt = $this->dbi->prepare($statement);
            $stmt->execute();
            $resultsIDs[] = $stmt->rowCount();
        }

        return $resultsIDs;
    }

    /**
     * @param string $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function getAll($sql, $bindings)
    {
        $stmt = $this->dbi->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
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
        $resultObject = $this->dbi->prepare('SELECT * FROM ' . $table . ' WHERE id = :id');
        $resultObject->execute(array(':id' => $id));
        return $resultObject->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getRow($sql, $bindings = array())
    {
        return Facade::getRow($sql, $bindings);
    }

    /**
     * @param string $table
     * @param int    $id
     *
     * @return void
     */
    public function deleteById($table, $id)
    {
        Facade::trash($table, $id);
    }

    /**
     * @param string $table
     * @param array  $rows
     *
     * @return void
     */
    public function deleteAll($table, $rows)
    {
//        foreach ($rows as $row) {
//            Facade::trash($table, $row['id']);
//        }
        array_map(function ($row) use ($table) {
            Facade::trash($table, $row['id']);
        }, $rows);
    }

    /**
     * Return an Array of Beans or empty array in case of no results
     *
     * @param string $table
     * @param string $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function find($table, $sql = null, $bindings = array())
    {
//        $returnArray = array();
        $beans = Facade::find($table, $sql, $bindings);
        if (!empty($beans)) {
//            foreach ($beans as $bean) {
//                $returnArray[] = $bean->export();
//            }
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
     * @param string $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function findOne($table, $sql = null, $bindings = array())
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
        return (int)$array[0];
    }

    /**
     * @param string $sql
     * @param array  $bindings
     *
     * @return int
     */
    public function exec($sql, $bindings = array())
    {
        return Facade::exec($sql, $bindings);
    }
}
