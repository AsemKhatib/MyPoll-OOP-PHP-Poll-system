<?php

namespace MyPoll\Classes;

use Exception;
use Twig_Environment;
use MyPoll\Classes\Database\DBInterface;

/**
 * Class Questions
 *
 * @package MyPoll\Classes
 */
class Questions extends FeaturesAbstract
{
    /** @var DBInterface */
    protected $db;

    /** @var Twig_Environment */
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
     * Questions constructor.
     *
     * @param DBInterface      $db
     * @param Twig_Environment $twig
     * @param Pagination       $pagination
     * @param Settings         $settings
     */
    public function __construct(DBInterface $db, Twig_Environment $twig, Pagination $pagination, Settings $settings)
    {
        $this->db = $db;
        $this->twig = $twig;
        $this->pagination = $pagination;
        $this->settings = $settings;
        $this->maxResults = $this->settings->getResultNumber();
    }

    /**
     * @return string
     */
    public function add()
    {
        return $this->twig->render('add_question.html');
    }

    /**
     * @return array
     */
    public function getPostParamsForAddMethod()
    {
        $question = General::cleanInput('string', $_POST['question']);
        $answers = General::cleanInput('array', $_POST['answer']);
        return array('question' => $question, 'answers' => $answers);
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    public function addExecute($paramsArray)
    {
        $qid = $this->db->getID($this->addQuestion($paramsArray));

        if (gettype($qid) != 'integer') {
            return 'Something went wrong while trying to add the question';
        }

        if (!$this->addAnswers($paramsArray['answers'], $qid)) {
            return 'Something went wrong while trying to add the answers of this question';
        }
        return 'Question Added successfully';
    }

    /**
     * @param array $paramsArray
     *
     * @return array|boolean
     */

    private function addQuestion($paramsArray)
    {
        $questionToAdd = $this->db->addRows('questions', array(array('question' => $paramsArray['question'])));
        $store = $this->db->store($questionToAdd);
        if (empty($store)) {
            return false;
        }
        return $store;
    }

    /**
     * @param array $answers
     * @param int   $qid
     *
     * @return boolean
     */
    private function addAnswers($answers, $qid)
    {
        $answersArray = array();
        foreach ($answers as $newAnswer) {
            $answersArray[] = array('qid' => $qid, 'answer' => $newAnswer);
        }
        $answersToAdd = $this->db->addRows('answers', $answersArray);
        $store = $this->db->store($answersToAdd);
        if (empty($store)) {
            return false;
        }
        return true;
    }

    /**
     * @param int $startPage
     *
     * @return string
     */
    public function show($startPage = 0)
    {
        $this->pagination->setParams('questions', $this->maxResults, $startPage, $this->db->count('questions'));
        return $this->twig->render(
            'show_questions.html',
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
     * @return string|boolean
     */
    public function showAnswers($qid, $is_pie)
    {
        $answers = $this->db->getAll('SELECT * FROM answers WHERE qid=:qid', [':qid' => $qid]);

        if (empty($answers)) {
            return false;
        }

        $this->processAnswersToShow($answers);

        return $this->twig->render('chat_bar.html', array(
            'answers_arr' => $this->answersArray,
            'percent' => $this->votesPercent,
            'is_pie' => $is_pie,
            'pie_arr' => $this->pieArray
        ));
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
        foreach ($this->votesArray as $num) {
            $this->votesPercent[] = @round($num / $sum * 100, 1);
        }

        $this->answersArray = "'" . implode("','", $this->answersArray) . "'";
        $this->votesPercent = implode(',', $this->votesPercent);
        $this->pieArray = implode(',', $this->pieArray);
    }

    /**
     * @param int $qid
     *
     * @return string
     */
    public function edit($qid)
    {
        $question = $this->db->getById('questions', $qid);

        if (empty($question)) {
            return General::ref($this->settings->getIndexPage());
        }

        $answers = $this->db->getAll('SELECT * FROM answers WHERE qid=? ORDER BY id', array($qid));
        return $this->renderEdit($qid, $question, $answers);
    }

    /**
     * @param int$qid
     * @param array $question
     * @param array $answers
     *
     * @return string
     */
    private function renderEdit($qid, $question, $answers)
    {
        return $this->twig->render('edit_question.html', array(
            'qid' => $qid,
            'question' => $question['question'],
            'answers' => $answers
        ));
    }

    /**
     * @return array
     */
    public function getPostParamsForEditMethod()
    {
        $qid = General::cleanInput('int', $_POST['qid']);
        $question = General::cleanInput('string', $_POST['question']);
        $answers = General::cleanInput('array', $_POST['answer']);
        return array('qid' => $qid, 'question' => $question, 'answers_old' => $answers);
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     */
    public function editExecute($paramsArray)
    {
        $questionUpdate = $this->db->getById('questions', $paramsArray['qid'], 'bean');
        $this->db->editRow($questionUpdate, array('question' => $paramsArray['question']));
        $questionStore = $this->db->store($questionUpdate);

        $editAnswers = $this->editAnswers($paramsArray['answers_old'], $paramsArray['qid']);

        if (empty($questionStore)) {
            return 'Something went wrong while trying to edit the question';
        }

        if (!$editAnswers) {
            return 'Something went wrong while trying to edit the answers of this question';
        }

        return 'Question edited successfully';
    }

    /**
     * @param array $answers
     * @param int   $qid
     *
     * @return boolean
     */
    private function editAnswers($answers, $qid)
    {
        foreach ($answers as $key => $value) {
            $answer = $this->db->getById('answers', $key, 'bean');
            $answer = $answer[0];

            if (empty($answer)) {
                $newAnswer = $this->db->addRows('answers', array(array('qid' => $qid, 'answer' => $value)));
                $store = $this->db->store($newAnswer);
                if (empty($store)) {
                    return false;
                }
            }

            if (!empty($answer) && $answer['answer'] != $value) {
                $newAnswer = $this->db->editRow(array($answer), array('answer' => $value));
                $store = $this->db->store($newAnswer);
                if (empty($store)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param int $qid
     *
     * @return string
     */
    public function delete($qid)
    {
        $this->db->deleteById('questions', $qid);
        $answersToDelete = $this->db->find('answers', 'qid = :qid', [':qid' => $qid]);
        $this->db->deleteAll('answers', $answersToDelete);
        return 'The question and all its answers were successfully deleted';
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function deleteAnswer($id)
    {
        $this->db->deleteById('answers', $id);
    }
}
