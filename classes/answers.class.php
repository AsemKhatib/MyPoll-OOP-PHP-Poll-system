<?php

namespace MyPoll\Classes;

use Exception;
use MyPoll\Classes\Database\DBInterface;

/**
 * Class Answers
 *
 * @package MyPoll\Classes
 */
class Answers
{
	/** @var DBInterface **/
	protected $database;

    /** @var  array */
    protected $votesArray = array();

    /** @var  array */
    protected $answersArray = array();

    /** @var  array */
    protected $votesPercent = array();

    /** @var  array */
    protected $pieArray = array();
	
	/**
     * Questions constructor.
     *
     * @param DBInterface      $database
     *
     **/
	public function __construct(DBInterface $database)
	{
		$this->database = $database;
	}

    /**
     * @param array $answers
     * @param int   $qid
     *
     * @return void
     *
     * @throws Exception
     */
    public function addAnswers($answers, $qid)
    {
        $answersArray = array_map(function ($newAnswer) use ($qid) {
            return array('qid' => $qid, 'answer' => $newAnswer);
        }, array_values($answers));

        $answersToAdd = $this->database->addRows('answers', $answersArray);
        $store = $this->database->store($answersToAdd);
        if (empty($store)) {
            throw new Exception('Something went wrong while trying to add the answers of the new question');
        }
    }


    /**
     * @param int    $qid
     * @param string $is_pie
     *
     * @return array|boolean
     */
    public function getAnswersChart($qid, $is_pie)
    {
        $answers = $this->database->getAll('SELECT * FROM answers WHERE qid=:qid', [':qid' => $qid]);

        if (empty($answers)) {
            return false;
        }

        $this->processAnswersToShow($answers);

        return array(
            'answers_arr' => $this->answersArray,
            'percent' => $this->votesPercent,
            'is_pie' => $is_pie,
            'pie_arr' => $this->pieArray
        );
    }

    /**
     * @param array $answers
     *
     * @return void
     */
    private function processAnswersToShow($answers)
    {
        foreach ($answers as $row) {
            $this->answersArray[] = $row['answer'];
            $this->votesArray[] = $row['votes'];
            $this->pieArray[] = "['" . $row['answer'] . "', " . $row['votes'] . "]";
        }

        $sum = array_sum($this->votesArray);
        $this->votesPercent = array_map(function ($num) use ($sum) {
            return @round($num / $sum * 100, 1);
        }, $this->votesArray);

        $this->answersArray = "'" . implode("','", $this->answersArray) . "'";
        $this->votesPercent = implode(',', $this->votesPercent);
        $this->pieArray = implode(',', $this->pieArray);
    }

    public function getAnswersForEdit($qid)
    {
    	return $this->database->getAll('SELECT * FROM answers WHERE qid=:qid ORDER BY id', [':qid' => $qid]);
    }


    /**
     * @param array $answers
     * @param int   $qid
     *
     * @return void
     */
    public function editAnswers($answers, $qid)
    {
        foreach ($answers as $key => $value) {
            $answer = $this->database->getById('answers', $key, 'bean');
            if (empty($answer[0])) {
                $this->addExtraAnswer($qid, $value);
            }
            if (!empty($answer[0]) && $answer[0]['answer'] != $value) {
                $this->editExistedAnswer($answer, $value);
            }
        }
    }

    /**
     * @param int $qid
     * @param string $value
     *
     * @throws Exception
     */
    private function addExtraAnswer($qid, $value)
    {
        $newAnswer = $this->database->addRows('answers', array(array('qid' => $qid, 'answer' => $value)));
        $store = $this->database->store($newAnswer);
        if (empty($store)) {
            throw new Exception('Something went wrong while trying to add new answers to this question');
        }
    }

    /**
     * @param array $answer
     * @param string $value
     *
     * @throws Exception
     */
    private function editExistedAnswer($answer, $value)
    {
        $newAnswer = $this->database->editRow(array($answer), array('answer' => $value));
        $store = $this->database->store($newAnswer);
        if (empty($store)) {
            throw new Exception('Something went wrong while trying to edit the answers of this question');
        }
    }

    public function deleteAllAnswers($qid)
    {
    	$answersToDelete = $this->database->find('answers', 'qid = :qid', [':qid' => $qid]);
        $this->database->deleteAll('answers', $answersToDelete);
    }


    /**
     * @param int $id
     *
     * @return void
     */
    public function deleteAnswer($id)
    {
        $this->database->deleteById('answers', $id);
    }
}