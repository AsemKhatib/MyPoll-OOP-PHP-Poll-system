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
class Login extends Cookie
{
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
     * @param Users       $users
     * @param Settings    $settings
     */
    public function __construct(DBInterface $db, Users $users, Settings $settings)
    {
        parent::__construct($db);
        $this->users = $users;
        $this->settings = $settings;
        $this->cookie = General::issetAndNotEmpty($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : null;
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
            $result = $this->db->getRow($query, [':user' => $user, ':pass' => $this->users->getHash($user)]);

            if (!password_verify($pass, $result['user_pass'])) return false;

            $this->dataSetter(array($result['user_name'], $result['id'],$result['email']));
            $this->authLogin();
            $this->setRememberme($this->userID);
            return true;
        }

        return false;
    }


    /**
     * @param array $data
     *
     * @return void
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

        if (!$cookie) return false;
        $userLog = $this->getRemembermeMeHash($cookie['token']);
        $user = $this->db->getById('users', $cookie['userID']);
        $user = $user[0];

        $this->dataSetter(array($user->user_name, $user->id,$user->email));

        $this->db->delete($userLog);
        $this->unsetCookie();
        $this->rememberMe = true;
        $this->setRememberme($user->id);
    }

    /**
     * @return boolean
     */
    public function isLoggedIn()
    {
        if ($this->isSessionExist()) {
            return true;
        }

        if ($this->isRememberme()) {
            $this->setupNewCredentials();
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
        if (General::issetAndNotEmpty($_SESSION['user']) && General::issetAndNotEmpty($_SESSION['id'])) return true;
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
            $this->db->delete($userLog);
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
            echo General::ref($this->settings->getIndexPage());
        }
    }
}
