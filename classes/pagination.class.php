<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;

/**
 * Class Pagination
 * @package MyPoll\Classes
 */
class Pagination
{

    /** @var  string */
    protected $DBTable;
    /** @var  int */
    protected $maxResults;
    /** @var  int */
    protected $startPage;
    /** @var  int */
    protected $storedNumber;

    /**
     * @param string $DBTable
     * @param int $startPage
     * @param int $maxResults
     */
    public function setParams($DBTable, $maxResults, $startPage)
    {
        $this->DBTable = $DBTable;
        $this->maxResults = $maxResults;
        $this->startPage = $startPage;
        $this->storedNumber = Facade::count($this->DBTable);
    }
    /**
     * @return array
     */
    public function getResults()
    {
        if ($this->storedNumber > $this->maxResults) {
            $startFrom = $this->maxResults * $this->startPage;
            $extraSQL = 'ORDER BY id ASC LIMIT ' . $startFrom . ',' . $this->maxResults;
            return Facade::findAll($this->DBTable, $extraSQL);
        } else {
            return Facade::findAll($this->DBTable);
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
