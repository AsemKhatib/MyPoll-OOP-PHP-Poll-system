<?php

namespace MyPoll\Classes;

use MyPoll\Classes\Database\DBInterface;
use MyPoll\Classes\Database\RedBeanDB;

/**
 * Class Pagination
 *
 * @package MyPoll\Classes
 */
class Pagination
{
    /** @var  RedBeanDB */
    protected $db;
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
     * @param DBInterface $db
     */
    public function __construct(DBInterface $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $DBTable
     * @param int    $startPage
     * @param int    $maxResults
     * @param int    $storedNumber
     */
    public function setParams($DBTable, $maxResults, $startPage, $storedNumber)
    {
        $this->DBTable = $DBTable;
        $this->maxResults = $maxResults;
        $this->startPage = $startPage;
        $this->storedNumber = $storedNumber;
    }

    /**
     * @return bool
     */
    public function prepareQuery()
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
     */
    public function getResults()
    {
        if (!$this->prepareQuery()) {
            return $this->db->getFinder()->find($this->DBTable);
        } else {
            return $this->db->getFinder()->find($this->DBTable, $this->prepareQuery());
        }
    }

    /**
     * @return int
     */
    public function getPagesNumber()
    {
        return @(int)ceil($this->storedNumber / $this->maxResults);
    }
}
