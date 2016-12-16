<?php

namespace MyPoll\Classes\Login;

use MyPoll\Classes\Database\DBInterface;
use MyPoll\Classes\Login\Cookie;
use MyPoll\Classes\Users;
use MyPoll\Classes\Settings;
use MyPoll\Classes\General;
use Exception;

/**
 * Class Login
 *
 * @package MyPoll\Classes
 */
class Login
{
    /** @var DBInterface */
    protected $db;

    /** @var Cookie */
    protected $cookie;

    /** @var  Users */
    protected $users;

    /** @var Settings */
    protected $settings;

    /** @var  string */
    private $userName;

    /** @var  int */
    private $userID;

    /** @var  string */
    private $email;

    /**
     * Login constructor.
     *
     * @param DBInterface $db
     * @param Cookie $cookie
     * @param Users       $users
     * @param Settings    $settings
     */
    public function __construct(DBInterface $db, Cookie $cookie, Users $users, Settings $settings)
    {
        $this->db = $db;
        $this->cookie = $cookie;
        $this->users = $users;
        $this->settings = $settings;
        $this->cookie->rememberMe = isset($_POST['rememberme']) ? true : false;
    }

    /**
     * @param string $user
     * @param string $pass
     *
     * @return bool
     */
    public function check($user, $pass)
    {
        if (General::issetAndNotEmpty($user) && General::issetAndNotEmpty($pass)) {
            $query = 'SELECT * FROM users WHERE user_name = :user AND user_pass = :pass';
            $result = $this->db->getRow($query, [':user' => $user, ':pass' => $this->users->getHash($user)]);
            if (!password_verify($pass, $result['user_pass'])) {
                return false;
            }
            $this->dataSetter(array($result['user_name'], $result['id'],$result['email']));
            $this->authLogin();
            $this->cookie->setRememberme($this->userID);
            return true;
        }

        return false;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    private function dataSetter($data)
    {
        list($userName, $userID, $email) = $data;
        $this->userName = $userName;
        $this->userID = $userID;
        $this->email = $email;
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    private function getUserUsingCookie()
    {
        $cookie = $this->cookie->getCookieData();
        $this->deleteOldUserLog($cookie);
        $user = $this->db->getById('users', $cookie['userID']);
        if (empty($user)) {
            throw new Exception('No user that matches the sent cookie has been found in the system');
        }
        return $user;
    }

    /**
     * @param array $cookie
     *
     * @return void
     */
    private function deleteOldUserLog($cookie)
    {
        $userLog = $this->cookie->getRemembermeMeHash($cookie['token']);
        $this->db->deleteById('rememberme', $userLog['id']);
    }

    /**
     * @return boolean
     */
    private function setupNewCredentials()
    {
        $user = $this->getUserUsingCookie();
        $this->dataSetter(array($user['user_name'], $user['id'] ,$user['email']));
        $this->cookie->unsetCookie();
        $this->rememberMe = true;
        $this->cookie->setRememberme($this->userID);
        return true;
    }

    /**
     * @return boolean
     */
    public function isLoggedIn()
    {
        if ($this->isSessionExist()) {
            return true;
        }

        if ($this->cookie->isRememberme() && $this->setupNewCredentials()) {
            $this->authLogin();
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    private function isSessionExist()
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
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
     * @return boolean
     */
    public function logout()
    {
        $cookie = $this->cookie->getCookieData();
        if ($cookie) {
            $this->deleteOldUserLog($cookie);
        }
        $this->cookie->unsetCookie();
        session_destroy();
        return true;
    }

    /**
     * @return string
     */
    public function checkIsNotLoggedIn()
    {
        if (!$this->isLoggedIn()) {
            return General::ref($this->settings->getIndexPage());
        }
    }
}
