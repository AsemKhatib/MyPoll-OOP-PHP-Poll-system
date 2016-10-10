<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;

/**
 * Class Login
 *
 * @package MyPoll\Classes
 */
class Login extends Cookie
{
    /** @var  Users */
    protected $users;

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
     * @param Factory $factory
     */
    public function __construct($factory)
    {
        $this->users = $factory->getUsersObj();
        $this->cookie = General::issetAndNotEmpty($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : null;
    }

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
     * @param string $user
     * @param string $pass
     *
     * @return bool
     */
    public function check($user, $pass)
    {
        $this->rememberMe = isset($_POST['rememberme']) ? true : false;

        if (General::issetAndNotEmpty($user) && General::issetAndNotEmpty($pass)) {
            $query = 'SELECT * FROM users WHERE user_name = :user AND user_pass = :pass';

            $result = Facade::getRow(
                $query,
                [':user' => $user, ':pass' => $this->users->getHash($user)]
            );

            if (!password_verify($pass, $result['user_pass'])) {
                return false;
            }

            $this->dataSetter(array($result['user_name'], $result['id'],$result['email']));
            $this->authLogin();
            $this->setRememberme($this->userID);
            return true;
        }

        return false;
    }


    /**
     * @param array $data
     */
    protected function dataSetter($data)
    {
        list($userName, $userID, $email) = $data;

        $this->userName = $userName;
        $this->userID = $userID;
        $this->email = $email;
    }

    /**
     * @return void|boolean
     */
    protected function setupNewCredentials()
    {
        $cookie = $this->getCookieData();

        if (!$cookie) {
            return false;
        }
        $userLog = $this->getRemembermeMeHash($cookie['token']);
        $user = Facade::load('users', $cookie['userID']);

        $this->dataSetter(array($user->user_name, $user->id,$user->email));
        Facade::trash($userLog);
        $this->unsetCookie();
        $this->setRememberme($user->id);
    }

    /**
     * @return boolean
     */
    public function isLoggedIn()
    {
        if ($this->isSessionExist()) {
            return true;
        } elseif ($this->isRememberme()) {
            $this->authLogin();
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
        if (General::issetAndNotEmpty($_SESSION['user']) && General::issetAndNotEmpty($_SESSION['id'])) {
            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    protected function authLogin()
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
        if ($this->getCookieData()) {
            $userLog = $this->getRemembermeMeHash($this->getCookieData()['token']);
            Facade::trash($userLog);
        }
        $this->unsetCookie();
        session_destroy();
    }

    /**
     * @return void|string
     */
    public function checkIsLoggedIn()
    {
        if (!$this->isLoggedIn()) {
            echo General::ref($this->getIndexPage());
        }
    }
}
