<?php

namespace MyPoll\Classes\Database\Entity;

/**
 * Class Answer
 *
 * @package MyPoll\Classes\Entity
 */
class Answer
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $qid;

    /**
     * @var string
     */
    protected $answer;

    /**
     * @var int
     */
    protected $votes;

    /**
     * Answer constructor.
     * @param int $id
     * @param int $qid
     * @param string $answer
     * @param int $votes
     */
    public function __construct($id, $qid, $answer, $votes)
    {
        $this->id = $id;
        $this->qid = $qid;
        $this->answer = $answer;
        $this->votes = $votes;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getQid(): int
    {
        return $this->qid;
    }

    /**
     * @return string
     */
    public function getAnswer(): string
    {
        return $this->answer;
    }

    /**
     * @return int
     */
    public function getVotes(): int
    {
        return $this->votes;
    }

}