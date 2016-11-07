<?php

namespace MyPoll\Classes\Entity;

/**
 * Class User
 *
 * @package MyPoll\Classes\Entity
 */
class User
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $user_name;

    /**
     * @var string
     */
    protected $user_pass;

    /**
     * @var string
     */
    protected $email;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @param string $user_name
     */
    public function setUserName($user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * @return string
     */
    public function getUserPass()
    {
        return $this->user_pass;
    }

    /**
     * @param string $user_pass
     */
    public function setUserPass($user_pass)
    {
        $this->user_pass = $user_pass;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
}