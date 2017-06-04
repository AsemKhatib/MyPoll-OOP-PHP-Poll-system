<?php

namespace MyPoll\Classes\Login;

use MyPoll\Classes\Database\DBInterface;
use Exception;

/**
 * Class RememberMe
 *
 * @package MyPoll\Classes\Login
 */
class RememberMe
{
    /** @var  DBInterface */
    protected $database;
    /**
     * RememberMe constructor.
     *
     * @param DBInterface $db
     */
    public function __construct(DBInterface $db)
    {
        $this->database = $db;
    }

    /**
     * @param array $cookie
     *
     * @return void
     */
    public function deleteOldUserLog($cookie)
    {
        $userLog = $this->getRemembermeMeHash($cookie['token']);
        $this->database->deleteById('rememberme', $userLog['id']);
    }


    /**
     * @param string $token
     *
     * @return array
     */
    public function getRemembermeMeHash($token)
    {
        return $this->database->findOne('rememberme', 'hash = :hash', [':hash' => $token]);
    }

    /**
     * @param int    $userID
     * @param string $token
     *
     * @return boolean
     *
     * @throws Exception
     */
    public function saveLogToDatabase($userID, $token)
    {
        $newLog = $this->database->addRows('rememberme', ['userid' => $userID, 'hash' => $token]);
        if (empty($this->database->store($newLog))) {
            throw new Exception('Something went wrong while trying to save cookie in the database');
        }
        return true;
    }
}