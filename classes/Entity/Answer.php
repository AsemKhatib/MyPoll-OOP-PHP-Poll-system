<?php

namespace MyPoll\Classes\Entity;

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
     * @return int
     */
    public function getQid()
    {
        return $this->qid;
    }

    /**
     * @param int $qid
     */
    public function setQid($qid)
    {
        $this->qid = $qid;
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return int
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param int $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }
}