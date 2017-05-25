<?php

namespace MyPoll\Classes\Database;

use PDO;
use PDOException;

/**
 * Class PDODB
 * @package MyPoll\Classes\Database
 */
class PDODB implements DBInterface
{
    /** @var  PDO */
    protected $dbi = false;

    /** @var string */

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
        $stmt[] = 'INSERT INTO ' . $table . ' ('. $key .') Values ('. $value .')';
        return $stmt;
    }

    /**
     * @param array $modelArray
     * @param array $dataArray
     *
     * @return array
     */
    public function editRow($modelArray, $dataArray)
    {
        $extraData = array('id' => $modelArray['id'], 'table_name' => $modelArray['table_name']);
        return array_walk($dataArray, array($this, 'editRowWalk'), $extraData);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param array $extraData
     *
     * @return array
     */
    public function editRowWalk($key, $value, $extraData)
    {
        $stmt[] = 'UPDATE ' . $extraData['table_name'] . ' SET '. $key .'='. $value .' WHERE id='. $extraData['id'] ;
        return $stmt;
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    public function store($rows)
    {
        $resultsIDs = [];
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
        $result = $this->dbi->prepare('SELECT * FROM ' . $table . ' WHERE id = :id');
        $result->execute(array(':id' => $id));
        $returnedArray = $result->fetch(PDO::FETCH_ASSOC);
        return array_merge($returnedArray, array('table_name' => $table));
    }

    /**
     * @param string $table
     * @param int    $id
     *
     * @return void
     */
    public function deleteById($table, $id)
    {
        $this->deleteCallBack($id, $table);
    }

    /**
     * @param string $table
     * @param array  $rows
     *
     * @return void
     */
    public function deleteAll($table, $rows)
    {
        array_walk($rows, array($this, 'deleteCallBack'), $table);
    }

    /**
     * @param array|int $rows
     * @param string $table
     */
    private function deleteCallBack($rows, $table)
    {
        $result = $this->dbi->prepare('DELETE FROM ' . $table . ' WHERE id = :id');
        $result->execute(array(':id' => $rows['id']));
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
    public function find($table, $sql, $bindings = array())
    {
        $query = $this->dbi->prepare('SELECT * FROM '. $table . ' WHERE ' . $sql);
        $query->execute($bindings);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return [];
        }
        return $result;
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
    public function findOne($table, $sql, $bindings = array())
    {
        $query = $this->dbi->prepare('SELECT * FROM '. $table . ' WHERE ' . $sql . ' LIMIT 1');
        $query->execute($bindings);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return [];
        }
        return $result;
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
        $query = $this->dbi->prepare($sql);
        $query->execute($bindings);
        return $query->rowCount();
    }
}
