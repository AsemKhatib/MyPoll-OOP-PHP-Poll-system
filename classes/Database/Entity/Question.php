<?php

namespace MyPoll\Classes\Database\Entity;

/**
 * Class Question
 *
 * @package MyPoll\Classes\Entity
 */
class Question
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $question;

    /**
     * Question constructor.
     * @param int $id
     * @param string $question
     */
    public function __construct($id, $question)
    {
        $this->id = $id;
        $this->question = $question;
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
    public function getQuestion(): string
    {
        return $this->question;
    }
}