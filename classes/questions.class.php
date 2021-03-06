<?php

namespace MyPoll\Classes;

use Exception;
use RedBeanPHP\Facade;

/**
 * Class Questions
 *
 * @package MyPoll\Classes
 */
class Questions extends FeaturesAbstract
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var Pagination */
    protected $pagination;

    /** @var  Settings */
    protected $settings;

    /** @var  array */
    protected $votesArray = array();

    /** @var  array */
    protected $answersArray = array();

    /** @var  array */
    protected $votesPercent = array();

    /** @var  array */
    protected $pieArray = array();

    /** @var int */
    protected $maxResults;

    /**
     * @param Factory $factory
     */
    public function __construct($factory)
    {
        $this->twig = $factory->getTwigAdminObj();
        $this->pagination = $factory->getPaginationObj();
        $this->settings = $factory->getSettingsObj();
        $this->maxResults = $this->settings->getResultNumber();
    }

    /**
     * @return string
     */
    public function add()
    {
        return $this->twig->display('add_poll.html');
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    public function addExecute($paramsArray)
    {
        try {
            // Adding the question first
            $questionToAdd = Facade::dispense('question');
            $questionToAdd->question = $paramsArray['question'];
            Facade::store($questionToAdd);
            $qid = $questionToAdd->getID();
            // Now we gonna add the Answers for this question :)
            $this->addAnswers($paramsArray['answers'], $qid);
            echo 'Question Added successfully';
        } catch (Exception $e) {
            echo 'Error :' . $e->getMessage();
        }
    }

    /**
     * @param array $answers
     * @param int   $qid
     *
     * @return void
     */
    private function addAnswers($answers, $qid)
    {
        foreach ($answers as $newAnswer) {
            $answersToAdd = Facade::dispense('answers');
            $answersToAdd->answer = $newAnswer;
            $answersToAdd->qid = $qid;
            Facade::store($answersToAdd);
        }
    }

    /**
     * @param int $startPage
     *
     * @return string
     */
    public function show($startPage = 0)
    {
        $this->pagination->setParams('questions', $this->maxResults, $startPage);
        return $this->twig->render(
            'show_poll.html',
            array(
                'resultsp' => $this->pagination->getResults(),
                'pagesNumber' => $this->pagination->getPagesNumber()
            )
        );
    }

    /**
     * @param int    $qid
     * @param string $is_pie
     *
     * @return string
     */
    public function showAnswers($qid, $is_pie)
    {
        $answers = Facade::getAll('SELECT * FROM answers WHERE qid=:qid', [':qid' => $qid]);

        foreach ($answers as $row) {
            $this->answersArray[] = $row['answer'];
            $this->votesArray[] = $row['votes'];
            $this->pieArray[] = "['" . $row['answer'] . "', " . $row['votes'] . "]";
        }

        $sum = array_sum($this->votesArray);
        foreach ($this->votesArray as $num) {
            $this->votesPercent[] = @round($num / $sum * 100, 1);
        }

        $this->answersArray = "'" . implode("','", $this->answersArray) . "'";
        $this->votesPercent = implode(',', $this->votesPercent);
        $this->pieArray = implode(',', $this->pieArray);

        return $this->twig->render('chat_bar.html', array(
            'answers_arr' => $this->answersArray,
            'percent' => $this->votesPercent,
            'is_pie' => $is_pie,
            'pie_arr' => $this->pieArray
        ));
    }

    /**
     * @param int $qid
     *
     * @return string
     */
    public function edit($qid)
    {
        $question = Facade::load('questions', $qid);
        if ($question->isEmpty()) {
            return General::ref($this->settings->getIndexPage());
        }
        $answers = Facade::getAll('SELECT * FROM answers WHERE qid=? ORDER BY id', array($qid));

        return $this->twig->render('edit_poll.html', array(
            'qid' => $qid,
            'question' => $question->question,
            'answers' => $answers
        ));
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    public function editExecute($paramsArray)
    {
        try {
            $questionUpdate = Facade::load('questions', $paramsArray['qid']);
            $questionUpdate->question = $paramsArray['question'];
            Facade::store($questionUpdate);

            // New answers will have always a random key generated to avoid interference with
            // existed keys => id's in the database
            $this->editAnswers($paramsArray['answers_old'], $paramsArray['qid']);
            echo "Question edited successfully";
        } catch (Exception $e) {
            echo 'Error :' . $e->getMessage();
        }
    }

    /**
     * @param array $answers
     * @param int   $qid
     *
     * @return void
     */
    private function editAnswers($answers, $qid)
    {
        foreach ($answers as $key => $value) {
            $answer = Facade::load('answers', $key);
            if ($answer->isEmpty()) {
                $newAnswer = Facade::dispense('answers');
                $newAnswer->answer = $value;
                $newAnswer->qid = $qid;
                Facade::store($newAnswer);
            }

            if ($answer->answer != $value) {
                $answer->answer = $value;
                Facade::store($answer);
            }
        }
    }

    /**
     * @param int $qid
     *
     * @return void
     */
    public function delete($qid)
    {
        Facade::trash('questions', $qid);
        $answersToDelete = Facade::findAll('answers', 'qid = :qid', [':qid' => $qid]);
        Facade::trashAll($answersToDelete);
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function deleteAnswer($id)
    {
        Facade::trash('answers', $id);
    }
}
