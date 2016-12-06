<?php

namespace MyPoll\Classes;

use MyPoll\Classes\Database\DBInterface;
use Exception;

/**
 * Class Pagination
 *
 * @package MyPoll\Classes
 */
class Pagination
{
    /** @var  DBInterface */
    protected $database;
    /** @var  string */
    protected $DBTable;
    /** @var  int */
    protected $maxResults;
    /** @var  int */
    protected $startPage;
    /** @var  int */
    protected $storedNumber;

    /**
     * Pagination constructor.
     *
     * @param DBInterface $database
     */
    public function __construct(DBInterface $database)
    {
        $this->database = $database;
    }

    /**
     * @param string $DBTable
     * @param int    $startPage
     * @param int    $maxResults
     * @param int    $storedNumber
     *
     * @return void
     */
    public function setParams($DBTable, $maxResults, $startPage, $storedNumber)
    {
        $this->DBTable = $DBTable;
        $this->maxResults = $maxResults;
        $this->startPage = $startPage;
        $this->storedNumber = $storedNumber;
    }

    /**
     * @return string|bool
     */
    private function prepareQuery()
    {
        if ($this->storedNumber > $this->maxResults) {
            $startFrom = $this->maxResults * $this->startPage;
            $extraSQL = 'ORDER BY id ASC LIMIT ' . $startFrom . ',' . $this->maxResults;
            return $extraSQL;
        }
        return false;
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getResults()
    {
        $result = $this->database->find($this->DBTable, $this->prepareQuery());
        if (empty($result)) {
            throw new Exception('An error occurred while trying to fetch rows for Pagination');
        }
        return $this->database->find($this->DBTable, $this->prepareQuery());
    }

    /**
     * @return int
     */
    public function getPagesNumber()
    {
        return @(int)ceil($this->storedNumber / $this->maxResults);
    }
}
