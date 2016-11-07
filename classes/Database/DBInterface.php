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
     * @param mixed  $db_options
     */
    public function __construct($db_dsn, $db_user, $db_pass, $db_options = '');

    /**
     * @return void
     */
    public function setup();

    /**
     * @param string $dbName   type of bean we are looking for
     * @param string $addSQL   additional SQL snippet
     * @param array  $bindings parameters to bind to SQL
     *
     * @return int
     */
    public function count($dbName, $addSQL = '', $bindings = array());

    /**
     * @param array $rows
     * @param string $type
     *
     * @return array
     */
    public function addRows($rows, $type);

    /**
     * @param array $type
     *
     * @return integer
     */
    public function store($type);

    /**
     * Convenience function to execute Queries directly.
     * Executes SQL.
     *
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getAll($sql, $bindings);


    /**
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getRow($sql, $bindings);

    /**
     * @param string $dbName
     * @param int    $id
     *
     * @return array
     */
    public function getById($dbName, $id);

    /**
     * @param string $table
     * @param int   $id
     *
     * @return void
     */
    public function deleteById($table, $id = null);

    /**
     * @param array $columns
     *
     * @return void
     */
    public function delete($columns);

    /**
     * @param string $type
     * @param string $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function find($type, $sql = null, $bindings = array());

    /**
     * @param string $type
     * @param string $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function findOne($type, $sql = null, $bindings = array());
}
