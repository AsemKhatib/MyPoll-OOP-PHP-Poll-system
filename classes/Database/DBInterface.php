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
     * @param string $table   type of bean we are looking for
     * @param string $addSQL   additional SQL snippet
     * @param array  $bindings parameters to bind to SQL
     *
     * @return int
     */
    public function count($table, $addSQL = '', $bindings = array());

    /**
     * @param string $table
     * @param array $rows
     *
     * @return array
     */
    public function addRows($table, $rows);

    /**
     * @param array $rows
     *
     * @return array
     */
    public function store($rows);

    /**
     * Convenience function to execute Queries directly and returns multidimensional array
     * Executes SQL
     *
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getAll($sql, $bindings);


    /**
     * Convenience function to execute Queries directly and returns multidimensional array
     * Executes SQL
     *
     * @param string $sql      SQL query to execute
     * @param array  $bindings a list of values to be bound to query parameters
     *
     * @return array
     */
    public function getRow($sql, $bindings);

    /**
     * @param string $table
     * @param int    $id
     *
     * @return array
     */
    public function getById($table, $id);

    /**
     * @param string $table
     * @param int   $id
     *
     * @return void
     */
    public function deleteById($table, $id = null);

    /**
     * @param array $rows
     *
     * @return void
     */
    public function delete($rows);

    /**
     * @param string $table
     * @param string $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function find($table, $sql = null, $bindings = array());

    /**
     * @param string $table
     * @param string $sql
     * @param array  $bindings
     *
     * @return array
     */
    public function findOne($table, $sql = null, $bindings = array());


    /**
     * @param array $array
     *
     * @return int
     */
    public function getID($array);

    /**
     * @param string $sql
     * @param array  $bindings
     *
     * @return int
     */
    public function exec($sql, $bindings = array());
}