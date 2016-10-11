<?php

namespace MyPoll\Tests\Unit\Classes;

use MyPoll\Classes\Pagination;
use PHPUnit_Framework_TestCase;

class PaginationTest extends PHPUnit_Framework_TestCase
{
    /** @var  Pagination */
    protected $pagination;

    protected function setUp()
    {
        $this->pagination = new Pagination();
    }

    public function testStartClass()
    {
        $this->assertNotEmpty($this->pagination);
    }
}
