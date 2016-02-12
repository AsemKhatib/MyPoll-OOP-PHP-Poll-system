<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;

/**
 * Class Login
 * @package MyPoll\Classes
 */
class Login
{
    /** @var  Settings */
    protected $settingsObj;

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
     * @param object $settingsObj
     */
    public function __construct($settingsObj)
    {
        $this->settingsObj = $settingsObj;
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
            user_name = :user AND user_pass = :pass", [':user' => $user, ':pass' => $this->getHash($user)]);
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
     * @return bool
     */
    private function isRememberme()
    {
        if (!empty($this->cookie)) {
            list ($userID, $token, $mac) = explode(':', $this->cookie);
            if (hash_equals(hash_hmac('sha256', $userID . ':' . $token, $this::SECRET_KEY), $mac)) {
                $userLog = Facade::findOne('rememberme', 'hash = :hash', [':hash' => $token]);
                if (!empty($userLog) && hash_equals($userLog->hash, $token)) {
                    $this->authLogin();
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * @return void
     */
    private function setupNewCredentials()
    {
        list ($userID, $token, $mac) = explode(':', $this->cookie);
        $userLog = Facade::findOne('rememberme', 'hash = :hash', [':hash' => $token]);
        $user = Facade::load('users', $userID);
        $this->userName = $user->user_name;
        $this->userID = $user->id;
        $this->email = $user->email;
        Facade::trash($userLog);
        setcookie($this->cookieName, '', time() - 3600);
        unset($this->cookie);
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
        $cookies = $userID . ':' . $token;
        $mac = hash_hmac('sha256', $cookies, $this::SECRET_KEY);
        $cookies .= ':' . $mac;
        setcookie($this->cookieName, $cookies);
    }

    /**
     * @param string $userName
     *
     * @return mixed|null
     */
    private function getHash($userName)
    {
        $result = Facade::findOne('users', 'user_name = :user', [':user' => $userName]);
        if (!empty($result)) {
            return $result['user_pass'];
        }
        return false;
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
     * @return void
     */
    public function logout()
    {
        list ($userID, $token, $mac) = explode(':', $this->cookie);
        $userLog = Facade::findOne('rememberme', 'hash = :hash', [':hash' => $token]);
        Facade::trash($userLog);
        unset($_COOKIE[$this->cookieName]);
        unset($this->cookie);
        setcookie($this->cookieName, '', time() - 3600);
        session_destroy();
    }
}
