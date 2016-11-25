<?php

namespace MyPoll\Tests\Unit\Classes;

use Mockery as m;
use MyPoll\Classes\Database\RedBeanDB;
use MyPoll\Classes\Login\Login;
use MyPoll\Classes\Settings;
use MyPoll\Classes\Users;
use PHPUnit_Framework_TestCase;

class LoginTest extends PHPUnit_Framework_TestCase
{

    /**
     * @param mixed $return
     *
     * @return RedBeanDB
     */
    private function getMockObject($return)
    {
        $database = m::mock(RedBeanDB::class);
        $database->shouldReceive('getRow')->once()->withAnyArgs()->andReturn($return);
        return $database;
    }

    private function getMockData()
    {
        return array();
    }

    private function getLogin()
    {
        return new Login(
            $this->getMockObject($this->getMockData()),
            m::mock(Users::class),
            m::mock(Settings::class)
        );
    }

    public function testCheck()
    {
        $login = $this->getLogin()->check('aaaa', 'bbbb');
    }
}
