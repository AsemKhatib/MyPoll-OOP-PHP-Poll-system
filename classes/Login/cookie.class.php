<?php

namespace MyPoll\Classes\Login;

use MyPoll\Classes\Database\DBInterface;

class Cookie
{
    /** @var DBInterface */
    protected $db;

    const SECRET_KEY = 'secretKeyHere';

    /** @var  string */
    protected $cookie;

    /** @var boolean */
    protected $rememberMe = false;

    /** @var  string */
    protected $cookieName = 'rememberme';

    /**
     * Cookie expiry time in minutes
     *
     * @var int
     */
    protected $cookieExpiryTime = 60;

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
    protected function setCookie()
    {
        if (isset($_COOKIE[$this->cookieName]) && !empty($_COOKIE[$this->cookieName])) {
            $this->cookie = $_COOKIE[$this->cookieName];
        }
    }

    /**
     * @return array|boolean
     */
    protected function getCookieData()
    {
        if (empty($this->cookie)) {
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
    protected function getRemembermeMeHash($token)
    {
        return $this->db->findOne('rememberme', 'hash = :hash', [':hash' => $token]);
    }

    /**
     * @return bool
     */
    protected function isRememberme()
    {
        if (!$this->getCookieData()) {
            return false;
        }

        $cookie = (array) $this->getCookieData();
        $hash_hmac = hash_hmac('sha256', $cookie['userID'] . ':' . $cookie['token'], $this::SECRET_KEY);

        if (!hash_equals($hash_hmac, $cookie['mac'])) {
            return false;
        }

        $userLog = $this->getRemembermeMeHash($cookie['token']);

        if (!hash_equals($userLog['hash'], $cookie['token'])) {
            return false;
        }

        return true;
    }

    /**
     * @param int $userID
     *
     * @return boolean
     */
    protected function setRememberme($userID)
    {
        if ($this->rememberMe) {
            $token = bin2hex(openssl_random_pseudo_bytes(128));
            if (!$this->saveToDatabase($userID, $token) || !$this->createCookie($userID, $token)) {
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * @param int $userID
     * @param string $token
     *
     * @return boolean
     */
    protected function saveToDatabase($userID, $token)
    {
        $newLog = $this->db->addRows('rememberme', array(array('userid' => $userID, 'hash' => $token)));
        if (!$this->db->store($newLog)) {
            return false;
        }
        return true;
    }

    /**
     * @param int $userID
     * @param string $token
     *
     * @return string
     */
    protected function generateCookieData($userID, $token)
    {
        $newCookie = $userID . ':' . $token;
        $mac = hash_hmac('sha256', $newCookie, $this::SECRET_KEY);
        $newCookie .= ':' . $mac;
        return $newCookie;
    }

    /**
     * @param int $userID
     * @param string $token
     *
     * @return boolean
     */
    protected function createCookie($userID, $token)
    {
        $cookieData = $this->generateCookieData($userID, $token);
        if (empty($cookieData)) {
            return false;
        }
        setcookie($this->cookieName, $cookieData, time()+60*$this->cookieExpiryTime);
        return true;
    }

    /**
     * @return void
     */
    protected function unsetCookie()
    {
        unset($_COOKIE[$this->cookieName]);
        unset($this->cookie);
        setcookie($this->cookieName, '', time() - 3600);
    }
}
