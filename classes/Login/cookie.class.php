<?php

namespace MyPoll\Classes\Login;

use RedBeanPHP\Facade;

class Cookie
{
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
     * @return \RedBeanPHP\OODBBean
     */
    protected function getRemembermeMeHash($token)
    {
        return Facade::findOne('rememberme', 'hash = :hash', [':hash' => $token]);
    }

    /**
     * @return bool
     */
    protected function isRememberme()
    {
        if (!$this->getCookieData()) {
            return false;
        }

        $cookie = $this->getCookieData();
        $hash_hmac = hash_hmac('sha256', $cookie['userID'] . ':' . $cookie['token'], $this::SECRET_KEY);

        if (!hash_equals($hash_hmac, $cookie['mac'])) {
            return false;
        }

        $userLog = $this->getRemembermeMeHash($cookie['token']);

        if (!hash_equals($userLog->hash, $cookie['token'])) {
            return false;
        }

        return true;
    }

    /**
     * @param int $userID
     *
     * @return void
     */
    protected function setRememberme($userID)
    {
        if ($this->rememberMe) {
            $token = bin2hex(openssl_random_pseudo_bytes(128));
            $newLog = Facade::dispense('rememberme');
            $newLog->userid = $userID;
            $newLog->hash = $token;
            Facade::store($newLog);
            $newCookie = $userID . ':' . $token;
            $mac = hash_hmac('sha256', $newCookie, $this::SECRET_KEY);
            $newCookie .= ':' . $mac;
            setcookie($this->cookieName, $newCookie, time()+60*$this->cookieExpiryTime);
        }
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
