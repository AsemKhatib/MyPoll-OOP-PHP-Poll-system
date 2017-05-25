<?php

namespace MyPoll\Classes\Login;

use MyPoll\Classes\Database\DBInterface;
use MyPoll\Classes\Users;
use MyPoll\Classes\Settings;
use MyPoll\Classes\General;

/**
 * Class Login
 *
 * @package MyPoll\Classes
 */
class Login
{
    /** @var DBInterface */
    protected $db;

    /** @var RememberMe  */
    protected $rememberMeObj;

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
     * @param RememberMe $rememberMeObj
     * @param Users       $users
     * @param Settings    $settings
     */
    public function __construct(DBInterface $db,RememberMe $rememberMeObj, Users $users, Settings $settings)
    {
        $this->db = $db;
        $this->rememberMeObj = $rememberMeObj;
        $this->cookie = new Cookie($this);
        $this->users = $users;
        $this->settings = $settings;
        $this->cookie->rememberMe = isset($_POST['rememberme']) ? true : false;
    }

    /**
     * @return RememberMe
     */
    public function getRememberMeObj()
    {
        return $this->rememberMeObj;
    }

    /**
     * @return int
     */
    public function getUserID()
    {
        return $this->userID;
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
            $where= 'user_name = :user AND user_pass = :pass';
            $binding = [':user' => $user, ':pass' => $this->users->getHash($user)];
            $result = $this->db->findOne('users', $where, $binding);
            if (!password_verify($pass, $result['user_pass'])) {
                return false;
            }
            $this->dataSetter(array($result['user_name'], $result['id'], $result['email']));
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
     * @return boolean
     */
    private function setupNewCredentials()
    {
        $user = $this->users->getUserUsingCookie($this->cookie->getCookieData());
        $this->dataSetter(array($user['user_name'], $user['id'] ,$user['email']));
        $this->unsetLoginCredentials();
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
            $this->cookie->authorizeNewLogin();
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
    public function authLogin()
    {
        $_SESSION['user'] = $this->userName;
        $_SESSION['id'] = $this->userID;
        $_SESSION['email'] = $this->email;
    }

    /**
     * @return boolean
     */
    public function unsetLoginCredentials()
    {
        $cookie = $this->cookie->getCookieData();
        if ($cookie) {
            $this->rememberMeObj->deleteOldUserLog($cookie);
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
