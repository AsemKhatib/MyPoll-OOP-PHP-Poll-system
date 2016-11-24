<?php

namespace MyPoll\Tests\Unit\Classes;

use Mockery as m;
use MyPoll\Classes\Database\RedBeanDB;
use MyPoll\Classes\Pagination;
use PHPUnit_Framework_TestCase;

class PaginationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @param mixed $return
     *
     * @return Pagination
     */
    private function getResultsMethod($return)
    {
        $database = m::mock(RedBeanDB::class);
        $database->shouldReceive('find')->once()->withAnyArgs()->andReturn($return);
        $pagination = new Pagination($database);

        return $pagination;
    }

    /**
     * @return Pagination
     */
    private function getPagesNumberMethod()
    {
        $database = m::mock(RedBeanDB::class);
        $pagination = new Pagination($database);

        return $pagination;
    }

    public function testGetResultsSuccess1()
    {
        $pagination = $this->getResultsMethod(array());
        $pagination->setParams('answer', 10, 0, 10);

        $this->assertInternalType('array', $pagination->getResults());
    }

    public function testGetResultsSuccess2()
    {
        $pagination = $this->getResultsMethod(array());
        $pagination->setParams('answer', 1, 0, 6);

        $this->assertInternalType('array', $pagination->getResults());
    }

    public function testGetResultsFail()
    {
        $pagination = $this->getResultsMethod(null);
        $pagination->setParams('answer', 1, 0, 6);

        $this->assertNotInternalType('array', $pagination->getResults());
    }

    public function testgetPagesNumberSuccess1()
    {
        $pagination = $this->getPagesNumberMethod();
        $pagination->setParams('answer', 10, 0, 6);

        $this->assertInternalType('int', $pagination->getPagesNumber());
        $this->assertEquals(1, $pagination->getPagesNumber());

    }

    public function testgetPagesNumberSuccess2()
    {
        $pagination = $this->getPagesNumberMethod();
        $pagination->setParams('answer', 1, 0, 6);

        $this->assertInternalType('int', $pagination->getPagesNumber());
        $this->assertEquals(6, $pagination->getPagesNumber());
    }

    public function testgetPagesNumberFail()
    {
        $pagination = $this->getPagesNumberMethod();
        $pagination->setParams('answer', 1, 0, 0);

        $this->assertInternalType('int', $pagination->getPagesNumber());
        $this->assertEquals(0, $pagination->getPagesNumber());
    }

}
