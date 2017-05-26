<?php

namespace MyPoll\Tests\Unit\Classes;

use MyPoll\Classes\Components\General;

class GeneralTest extends \PHPUnit_Framework_TestCase
{
    public function testIssetAndNotEmptySuccess()
    {
        $abc = 'abc';
        $this->assertTrue(General::issetAndNotEmpty($abc));
    }

    public function testIssetAndNotEmptyFailWithEmptyValue()
    {
        $abc = '';
        $this->assertFalse(General::issetAndNotEmpty($abc));
    }

    /**
     * @expectedException \PHPUnit_Framework_Error_Notice
     */
    public function testIssetAndNotEmptyFailWithNotSetVarAndError()
    {
        General::issetAndNotEmpty($abc);
    }

    public function testIssetAndNotEmptyFailWithNotSetVar()
    {
        $this->assertFalse(@General::issetAndNotEmpty($abc));
    }

    public function testRefSuccess()
    {
        $this->assertEquals(
            '<meta http-equiv="refresh" content="0; url=index.php">',
            General::ref('index.php')
        );
    }

    public function testRefFail()
    {
        $this->assertNotEquals(
            '<meta http-equiv="refresh" content="0; url=index.php">',
            @General::ref($url)
        );
    }

    /**
     * @expectedException \PHPUnit_Framework_Error_Notice
     */
    public function testRefFailAndError()
    {
        General::ref($url);
    }

    public function testMessageSentSuccessWithMsgAndURL()
    {
        $return = '<meta http-equiv="refresh" content="2; url=index.php">Message Sent';
        $this->assertEquals(
            $return,
            General::messageSent('Message Sent', 'index.php')
        );
    }

    public function testMessageSentSuccessWithMsgOnly()
    {
        $return = 'Message Sent';
        $this->assertEquals(
            $return,
            General::messageSent('Message Sent')
        );
    }

    public function testMessageSentFailWithoutMsg()
    {
        $this->assertFalse(General::messageSent(''));
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testMessageSentFailWithoutMsgVar()
    {
        General::messageSent();
    }

    public function testCleanInputSuccessWithString()
    {
        $this->assertInternalType('string', General::cleanInput('string', 'ABC'));
    }

    public function testCleanInputFailNoType()
    {
        $this->assertFalse(General::cleanInput('NoType', 'aaaa'));
    }

    public function testPrintException()
    {
        function testException()
        {
            return new \Exception('Exception Test');
        }

        $this->assertEquals(
            'Error :Exception Test In /var/www/public/mypoll/tests/unit/classes/GeneralTest.php And line 103',
            General::printException(testException())
        );
    }

}
