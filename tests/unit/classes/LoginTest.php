<?php

namespace MyPoll\Tests\Unit\Classes;

use DI\Container;
use Mockery as m;
use MyPoll\Classes\Database\RedBeanDB;
use MyPoll\Classes\Login\Cookie;
use MyPoll\Classes\Login\Login;
use MyPoll\Classes\Login\RememberMe;
use MyPoll\Classes\Settings;
use MyPoll\Classes\Users;
use PHPUnit_Framework_TestCase;
use Exception;

class LoginTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        parent::setUp();
        global  $container;
        $this->container = $container;
    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * @param $extraArray array
     * @param mixed $return
     *
     * @return RedBeanDB
     */
    private function getMockObject($return, $extraArray = null)
    {
        $database = m::mock('MyPoll\Classes\Database\RedBeanDB');
        $database->shouldReceive('getRow')->once()->withAnyArgs()->andReturn($return);
        if ($extraArray) {
            foreach ($extraArray as $item) {
                $database->shouldReceive($item['method'])->once()->withAnyArgs()->andReturn($item['return']);
            }
        }
        return $database;
    }

    private function getMockData()
    {
        return array(
            'id' => 1,
            'email' => 'email@d.com',
            'user_name' => 'user',
            'user_pass' => password_hash('password', PASSWORD_DEFAULT)
        );
    }

    /**
     * @param $extraArray array
     * @param $badArray boolean
     *
     * @return Login
     */
    private function getLogin($extraArray = null, $badArray = null)
    {
        if ($badArray == true) {
            $data = $this->getMockData();
            $data['id'] = 'ABC';
        } else {
            $data = $this->getMockData();
        }
        $login =  new Login(
            $this->getMockObject($data, $extraArray),
            $this->getRememberMeObj($extraArray),
            $this->container->get(Users::class),
            $this->container->get(Settings::class)
        );

        return $login;
    }

    /**
     * @param array $extraArray
     *
     * @return RememberMe
     */
    private function getRememberMeObj($extraArray = null)
    {
        $rememberMe =  new RememberMe(
            $this->getMockObject($this->getMockData(), $extraArray)
        );

        return $rememberMe;
    }

    public function testCheckSuccess()
    {
        $this->assertEquals(true, $this->getLogin()->check('user', 'password'));
    }

    public function testCheckFailWrongPassword()
    {
        $this->assertEquals(false, $this->getLogin()->check('user', 'wrongpassword'));
    }

    public function testCheckFailNoUserAndPasswordSet()
    {
        $this->assertEquals(false, $this->getLogin()->check('', ''));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Something went wrong while trying to save cookie in the database
     */
    public function testCheckFailWithRememberMeAndSaveToDatabase()
    {
        $extraArray = array(
            array('method' => 'addRows', 'return' => null),
            array('method' => 'store', 'return' => array())
        );
        $_POST['rememberme'] = true;
        $login = $this->getLogin($extraArray);
        $login->check('user', 'password');
    }

    public function testIsLoggedInSuccessWithSession()
    {
        $this->getLogin()->check('user', 'password');
        $this->assertEquals(true, $this->getLogin()->isLoggedIn());
    }

    public function testIsLoggedInSuccessWithCookie()
    {
        $cookie = '1:44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6:f767b286550f691170905287f4fc0c5234928d3975ca29f5a55a5e59d919d53a';
        $token = '44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6';

        $extraArray = array(
            array('method' => 'addRows', 'return' => true),
            array('method' => 'store', 'return' => true),
            array('method' => 'findOne', 'return' => array('id' => 1, 'userid' => 1, 'hash' => $token)),
            array('method' => 'getById', 'return' => $this->getMockData()),
            array('method' => 'deleteById', 'return' => null)
        );
        $_POST['rememberme'] = true;
        $_COOKIE['rememberme'] = $cookie;
        $login = $this->getLogin($extraArray);
        $this->assertTrue(@$login->isLoggedIn());
    }

    public function testIsLoggedInFailWithMissingCookieInIsRememberme()
    {
        $cookie = '';
        $token = '44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6';

        $extraArray = array(
            array('method' => 'addRows', 'return' => true),
            array('method' => 'store', 'return' => true),
            array('method' => 'findOne', 'return' => array('id' => 1, 'userid' => 1, 'hash' => $token)),
            array('method' => 'getById', 'return' => $this->getMockData()),
            array('method' => 'deleteById', 'return' => null)
        );
        $_POST['rememberme'] = true;
        $_COOKIE['rememberme'] = $cookie;
        $login = $this->getLogin($extraArray);
        $this->assertFalse($login->isLoggedIn());
    }

    public function testIsLoggedInFailWithHackedCookieInIsRememberme()
    {
        $cookie = '1:44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6:bad3523523hash45634mac';
        $token = '44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6';

        $extraArray = array(
            array('method' => 'addRows', 'return' => true),
            array('method' => 'store', 'return' => true),
            array('method' => 'findOne', 'return' => array('id' => 1, 'userid' => 1, 'hash' => $token)),
            array('method' => 'getById', 'return' => $this->getMockData()),
            array('method' => 'deleteById', 'return' => null)
        );
        $_POST['rememberme'] = true;
        $_COOKIE['rememberme'] = $cookie;
        $login = $this->getLogin($extraArray);
        $this->assertFalse($login->isLoggedIn());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage No records that matches this cookie hash has been found in the system
     */
    public function testIsLoggedInFailWithMissingDatabaseTokenInIsRememberme()
    {
        $cookie = '1:44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6:f767b286550f691170905287f4fc0c5234928d3975ca29f5a55a5e59d919d53a';

        $extraArray = array(
            array('method' => 'addRows', 'return' => true),
            array('method' => 'store', 'return' => true),
            array('method' => 'findOne', 'return' => array()),
            array('method' => 'getById', 'return' => $this->getMockData()),
            array('method' => 'deleteById', 'return' => null)
        );
        $_POST['rememberme'] = true;
        $_COOKIE['rememberme'] = $cookie;
        $this->getLogin($extraArray)->isLoggedIn();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage No user that matches the sent cookie has been found in the system
     */
    public function testIsLoggedInFailWithMissingUserIDThatIncludedInTheCookieInSetupNewCredentials()
    {
        $cookie = '169:44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6:0d8a82eeddc4dacc7eb9223f4009faf346c634447144815f699fbd97e618b02b';
        $token = '44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6';

        $extraArray = array(
            array('method' => 'addRows', 'return' => true),
            array('method' => 'store', 'return' => true),
            array('method' => 'findOne', 'return' => array('id' => 100, 'userid' => 169, 'hash' => $token)),
            array('method' => 'getById', 'return' => array()),
            array('method' => 'deleteById', 'return' => null)
        );

        $_POST['rememberme'] = true;
        $_COOKIE['rememberme'] = $cookie;
        $this->getLogin($extraArray)->isLoggedIn();
    }

    public function testLogoutSuccessWithSession()
    {
        $this->assertNotFalse(@$this->getLogin()->unsetLoginCredentials());
    }

    public function testLogoutSuccessWithCookie()
    {
        $cookie = '1:44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6:f767b286550f691170905287f4fc0c5234928d3975ca29f5a55a5e59d919d53a';
        $token = '44671f25fd6f923905be57edd0caf7457b6f21bd6f940c31114f9b2f54bf997e7597691c0090c8d99a5e68f24a4c1c8ac19ccc175f521b164ddbd3244f8f7a4da8bc7688d7387f2ea5d6bf7c2ed93f4abfbe124fc4eafa220a05da12085b2db389b4daf572205c319039fb2b59e0e639acb80229a4e0686ba862827898f8dba6';

        $extraArray = array(
            array('method' => 'findOne', 'return' => array('id' => 1, 'userid' => 1, 'hash' => $token)),
            array('method' => 'deleteById', 'return' => null)
        );
        $_POST['rememberme'] = true;
        $_COOKIE['rememberme'] = $cookie;
        $login = $this->getLogin($extraArray);
        $this->assertNotFalse(@$login->unsetLoginCredentials());
    }

    public function testCheckIsNotLoggedInInSuccess()
    {
        $this->getLogin()->isLoggedIn();
        $this->assertEquals(
            '<meta http-equiv="refresh" content="0; url=index.php">',
            $this->getLogin()->checkIsNotLoggedIn()
        );
    }

    public function testCheckIsNotLoggedInInSuccess2()
    {
        $this->getLogin()->check('user', 'password');
        $this->assertEmpty(
            $this->getLogin()->checkIsNotLoggedIn()
        );
    }

}
