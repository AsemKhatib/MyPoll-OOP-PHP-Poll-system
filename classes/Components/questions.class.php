<?php

namespace MyPoll\Classes\Components;

use Exception;
use MyPoll\Classes\FeaturesAbstract;
use MyPoll\Classes\Pagination;
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

    /** @var Answers */
    protected $answers;

    /** @var Twig_Environment */
    protected $twig;

    /** @var Pagination */
    protected $pagination;

    /** @var  Settings */
    protected $settings;

    /** @var int */
    protected $maxResults;

    /**
     * Questions constructor.
     *
     * @param DBInterface $db
     * @param Answers $answers
     * @param Twig_Environment $twig
     * @param Pagination $pagination
     * @param Settings $settings
     */
    public function __construct(DBInterface $db, Answers $answers, Twig_Environment $twig, Pagination $pagination, Settings $settings)
    {
        $this->db = $db;
        $this->answers = $answers;
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
        return ['question' => $question, 'answers' => $answers];
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     *
     * @throws Exception
     */
    public function addExecute($paramsArray)
    {
        $this->addQuestion($paramsArray);
        $qid = $this->db->getLastID('questions');
        $this->answers->addAnswers($paramsArray['answers'], $qid);
        return 'Question Added successfully';
    }

    /**
     * @param array $paramsArray
     *
     * @return array
     *
     * @throws Exception
     */

    private function addQuestion($paramsArray)
    {
        $questionToAdd = $this->db->addRows('questions', ['question' => $paramsArray['question']]);
        $store = $this->db->store($questionToAdd);
        if (empty($store)) {
            throw new Exception('Something went wrong while trying to add the question');
        }
        return $store;
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
            [
                'resultsp' => $this->pagination->getResults(),
                'pagesNumber' => $this->pagination->getPagesNumber()
            ]
        );
    }

    /**
     * @param int $qid
     * @param string $is_pie
     *
     * @return string|boolean
     */
    public function showAnswers($qid, $is_pie)
    {
        $getChartArray = $this->answers->getAnswersChart($qid, $is_pie);

        if (!$getChartArray) {
            return false;
        }

        return $this->twig->render('chart_bar.html', $getChartArray);
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

        $answers = $this->answers->getAnswersForEdit($qid);
        return $this->renderEdit($qid, $question, $answers);
    }

    /**
     * @param int $qid
     * @param array $question
     * @param array $answers
     *
     * @return string
     */
    private function renderEdit($qid, $question, $answers)
    {
        return $this->twig->render('edit_question.html', [
            'qid' => $qid,
            'question' => $question['question'],
            'answers' => $answers
        ]);
    }

    /**
     * @return array
     */
    public function getPostParamsForEditMethod()
    {
        $qid = General::cleanInput('int', $_POST['qid']);
        $question = General::cleanInput('string', $_POST['question']);
        $answers = General::cleanInput('array', $_POST['answer']);
        return ['qid' => $qid, 'question' => $question, 'answers_old' => $answers];
    }

    /**
     * @param array $paramsArray
     *
     * @return string
     *
     * @throws Exception
     */
    public function editExecute($paramsArray)
    {
        $getQuestionToUpdate = $this->db->getById('questions', $paramsArray['qid']);
        $updateQuestion = $this->db->editRow($getQuestionToUpdate, ['question' => $paramsArray['question']]);
        $questionStore = $this->db->store($updateQuestion);

        if (empty($questionStore)) {
            throw new Exception('Something went wrong while trying to edit the question');
        }

        $this->answers->editAnswers($paramsArray['answers_old'], $paramsArray['qid']);
        return 'Question edited successfully';
    }

    /**
     * @param int $qid
     *
     * @return string
     */
    public function delete($qid)
    {
        $this->db->deleteById('questions', $qid);
        $this->answers->deleteAllAnswers($qid);
        return 'The question and all its answers were successfully deleted';
    }
}
