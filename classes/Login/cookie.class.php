<?php

namespace MyPoll\Classes\Login;

use MyPoll\Classes\Database\DBInterface;
use Exception;

class Cookie
{
    /** @var DBInterface */
    private $db;

    /** @info secret Key to hash the cookie */
    const SECRET_KEY = 'secretKeyHere';

    /** @var  string */
    protected $cookie;

    /** @var boolean */
    public $rememberMe = false;

    /** @var  string */
    protected $cookieName = 'rememberme';

    /**
     * Cookie expiry time in minutes
     *
     * @var int
     */
    private $cookieExpiryTime = 60;

    /**
     * Cookie constructor.
     *
     * @param DBInterface $db
     */
    public function __construct(DBInterface $db)
    {
        $this->db = $db;
        $this->setCookie();
    }

    /**
     * @return void
     */
    private function setCookie()
    {
        if (isset($_COOKIE[$this->cookieName]) && !empty($_COOKIE[$this->cookieName])) {
            $this->cookie = $_COOKIE[$this->cookieName];
        }
    }

    /**
     * @return array|boolean
     */
    public function getCookieData()
    {
        if (empty($this->cookie) || !preg_match('/^([0-9]+)(:){1}([a-z0-9]+)(:){1}([a-z0-9]+)$/m', $this->cookie)) {
            return false;
        }

        list ($userID, $token, $mac) = explode(':', $this->cookie);
        $cookieArray = array('userID' => $userID, 'token' => $token, 'mac' => $mac);
        return $cookieArray;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    public function getRemembermeMeHash($token)
    {
        return $this->db->findOne('rememberme', 'hash = :hash', [':hash' => $token]);
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function isRememberme()
    {
        $cookie = $this->getCookieData();
        if (!$cookie) {
            return false;
        }
        $hash_hmac = hash_hmac('sha256', $cookie['userID'] . ':' . $cookie['token'], $this::SECRET_KEY);
        $userLog = $this->getRemembermeMeHash($cookie['token']);

        if (empty($userLog)) {
            throw new Exception('No records that matches this cookie hash has been found in the system');
        }

        if (!hash_equals($hash_hmac, $cookie['mac']) || !hash_equals($userLog['hash'], $cookie['token'])) {
            return false;
        }
        return true;
    }

    /**
     * @param int $userID
     *
     * @return void
     */
    public function setRememberme($userID)
    {
        if ($this->rememberMe) {
            $token = bin2hex(openssl_random_pseudo_bytes(128));
            $this->saveToDatabase($userID, $token);
            $this->createCookie($userID, $token);
        }
    }

    /**
     * @param int    $userID
     * @param string $token
     *
     * @return boolean
     *
     * @throws Exception
     */
    private function saveToDatabase($userID, $token)
    {
        $newLog = $this->db->addRows('rememberme', array(array('userid' => $userID, 'hash' => $token)));
        if (empty($this->db->store($newLog))) {
            throw new Exception('Something went wrong while trying to save cookie in the database');
        }
        return true;
    }

    /**
     * @param int    $userID
     * @param string $token
     *
     * @return string
     */
    private function generateCookieData($userID, $token)
    {
        $newCookie = $userID . ':' . $token;
        $mac = hash_hmac('sha256', $newCookie, $this::SECRET_KEY);
        $newCookie .= ':' . $mac;
        return $newCookie;
    }

    /**
     * @param int    $userID
     * @param string $token
     *
     * @return boolean
     */
    private function createCookie($userID, $token)
    {
        $cookieData = $this->generateCookieData($userID, $token);
        setcookie($this->cookieName, $cookieData, time() + 60 * $this->cookieExpiryTime);
        return true;
    }

    /**
     * @return void
     */
    public function unsetCookie()
    {
        unset($_COOKIE[$this->cookieName]);
        unset($this->cookie);
        setcookie($this->cookieName, '', time() - 3600);
    }
}
