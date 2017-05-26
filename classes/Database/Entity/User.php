<?php

namespace MyPoll\Classes\Database\Entity;

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
     * User constructor.
     * @param int $id
     * @param string $user_name
     * @param string $user_pass
     * @param string $email
     */
    public function __construct($id, $user_name, $user_pass, $email)
    {
        $this->id = $id;
        $this->user_name = $user_name;
        $this->user_pass = $user_pass;
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->user_name;
    }

    /**
     * @return string
     */
    public function getUserPass(): string
    {
        return $this->user_pass;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}