<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;

/**
 * Class Login
 * @package MyPoll\Classes
 */
class Login
{
    /** @var  Factory */
    protected $factory;

    /** @var boolean */
    private $rememberMe = false;

    /** @var  string */
    private $cookie;

    /** @var  string */
    private $cookieName = 'rememberme';

    const SECRET_KEY = 'secret';

    /** @var string */
    private $logPage = "index.php?do=questions";

    /** @var string */
    private $indexPage = 'index.php';

    /** @var  string */
    private $userName;

    /** @var  int */
    private $userID;

    /** @var  string */
    private $email;

    /**
     * @return string
     */
    public function getIndexPage()
    {
        return $this->indexPage;
    }

    /**
     * @return string
     */
    public function getLogPage()
    {
        return $this->logPage;
    }

    /**
     * @param Factory $factory
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
        $this->cookie = isset($_COOKIE[$this->cookieName]) &&
        !empty($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : '';
    }

    /**
     * @param string $user
     * @param string $pass
     */
    public function check($user, $pass)
    {
        $this->rememberMe = isset($_POST['rememberme']) ? true : false;

        if (isset($user) && !empty($user) && isset($pass) && !empty($pass)) {
            $result = Facade::getRow("SELECT * FROM users WHERE
            user_name = :user AND user_pass = :pass", [':user' => $user, ':pass' => $this->factory->getUsersObj()->getHash($user)]);
            if (!empty($result) && password_verify($pass, $result['user_pass'])) {
                $this->userName = $result['user_name'];
                $this->userID = $result['id'];
                $this->email = $result['email'];
                $this->authLogin();
                if ($this->rememberMe) {
                    $this->setRememberme($this->userID);
                }
                echo General::Ref($this->logPage);
            } else {
                echo General::messageSent('Wrong Username or Password', $this->indexPage);
            }
        }
    }

    /**
     * @return array|boolean
     */
    private function getCookieData()
    {
        if (empty($this->cookie)) {
            return false;
        }

        list ($userID, $token, $mac) = explode(':', $this->cookie);
        $cookieArray = array('userID' => $userID, 'token' => $token, 'mac' => $mac);
        return $cookieArray;
    }

    /**
     * @return bool
     */
    private function isRememberme()
    {
        if ($this->getCookieData() != false) {
            if (hash_equals(hash_hmac('sha256', $this->getCookieData()['userID'] . ':' . $this->getCookieData()['token'], $this::SECRET_KEY), $this->getCookieData()['mac'])) {
                $userLog = Facade::findOne('rememberme', 'hash = :hash', [':hash' => $this->getCookieData()['token']]);
                if (!empty($userLog) && hash_equals($userLog->hash, $this->getCookieData()['token'])) {
                    $this->authLogin();
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * @return void|boolean
     */
    private function setupNewCredentials()
    {
        if (!$this->getCookieData()) {
            return false;
        }
        $userLog = Facade::findOne('rememberme', 'hash = :hash', [':hash' => $this->getCookieData()['token']]);
        $user = Facade::load('users', $this->getCookieData()['userID']);
        $this->userName = $user->user_name;
        $this->userID = $user->id;
        $this->email = $user->email;
        Facade::trash($userLog);
        $this->unsetCookie();
        $this->setRememberme($this->userID);
    }

    /**
     * @param int $userID
     *
     * @return void
     */
    private function setRememberme($userID)
    {
        $token = bin2hex(openssl_random_pseudo_bytes(128));
        $newLog = Facade::dispense('rememberme');
        $newLog->userid = $userID;
        $newLog->hash = $token;
        Facade::store($newLog);
        $newCookie = $userID . ':' . $token;
        $mac = hash_hmac('sha256', $newCookie, $this::SECRET_KEY);
        $newCookie .= ':' . $mac;
        setcookie($this->cookieName, $newCookie);
    }

    /**
     * @return boolean
     */
    public function isLoggedIn()
    {
        if ($this->isSessionExist() == true) {
            return true;
        } elseif ($this->isRememberme() == true) {
            $this->setupNewCredentials();
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    private function isSessionExist()
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user']) && isset($_SESSION['id']) &&
            !empty($_SESSION['id'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    private function authLogin()
    {
        $_SESSION['user'] = $this->userName;
        $_SESSION['id'] = $this->userID;
        $_SESSION['email'] = $this->email;
    }

    /**
     *
     */
    private function unsetCookie()
    {
        unset($_COOKIE[$this->cookieName]);
        unset($this->cookie);
        setcookie($this->cookieName, '', time() - 3600);
    }
    /**
     * @return void
     */
    public function logout()
    {
        if ($this->getCookieData() != false) {
            $userLog = Facade::findOne('rememberme', 'hash = :hash', [':hash' => $this->getCookieData()['token']]);
            Facade::trash($userLog);
        }
        $this->unsetCookie();
        session_destroy();
    }

    /**
     *
     */
    public function checkIsLoggedIn()
    {
        if (!$this->isLoggedIn()) {
            echo General::ref($this->factory->getLoginObj()->getIndexPage());
        }
    }
}
