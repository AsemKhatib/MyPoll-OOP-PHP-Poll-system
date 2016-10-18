<?php

namespace MyPoll\Tests\Unit\Classes;

use MyPoll\Classes\Pagination;
use PHPUnit_Framework_TestCase;

class PaginationTest extends PHPUnit_Framework_TestCase
{
    /** @var  Pagination */
    protected $pagination;

    /**
     * @param string $table
     * @param string $method
     * @param mixed $return
     *
     * @return \PHPUnit_Framework_MockObject_Builder_InvocationMocker|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function finderMock($table, $method, $return)
    {
        $finderMock = $this->createMock('\RedBeanPHP\Finder');
        $finderMock = $finderMock
            ->expects(self::any())
            ->method($method)
            ->with($table)
            ->will($this->returnValue($return));
        return $finderMock;
    }

    /**
     * @param string $method
     * @param mixed $return
     *
     * @return \PHPUnit_Framework_MockObject_Builder_InvocationMocker|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function dbMock($method, $return)
    {
        $dbMock = $this->createMock('\MyPoll\Classes\RedBeanDB');
        $dbMock = $dbMock
            ->expects(self::any())
            ->method($method)
            ->will($this->returnValue($return));
        return $dbMock;
    }

    protected function setUp()
    {
        $a = 0;
    }

    public function testGetResults()
    {
        $this->pagination = new Pagination($this->dbMock('getFinder', $this->finderMock('questions', 'find', 7)));
        $this->pagination->setParams('questions', 3, 1, 2);
        $this->assertInternalType('int', $this->pagination->getResults());
        $this->assertEquals(7, $this->pagination->getResults());
    }
}
