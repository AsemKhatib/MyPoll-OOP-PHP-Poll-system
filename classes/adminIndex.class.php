<?php

namespace MyPoll\Classes;

/**
 * Class AdminIndex
 * @package MyPoll\Classes
 */

class AdminIndex
{
    /** @var  Factory */
    protected $factory;

    /**
     * @param $factory
     */
    public function __construct($factory)
    {
        $this->factory = $factory;

        $this->factory->getTwigAdminObj()->addGlobal('session', $_SESSION);
        $this->factory->getSettingsObj()->checkCache();
        // add general settings to the global scope of Twig
        $this->factory->getTwigAdminObj()->addGlobal('site_maxanswers', $this->factory->getSettingsObj()->getSiteMaxAnswers());
        $this->factory->getTwigAdminObj()->addGlobal('site_name', $this->factory->getSettingsObj()->getSiteName());
    }

    /**
     * @return string
     */
    public function questions()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();
        $startPage = isset($_GET['startPage']) ? General::cleanInput('int', $_GET['startPage']) : null;
        echo $this->factory->getQuestionsObj()->show($startPage);
    }

    /**
     * @return string
     */
    public function users()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();
        $startPage = isset($_GET['startPage']) ? General::cleanInput('int', $_GET['startPage']) : null;
        echo $this->factory->getUsersObj()->show($startPage);
    }

    /**
     * @return string
     */
    public function settings()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();
        echo $this->factory->getSettingsObj()->edit();
    }

    /**
     * @return void|string
     */
    public function check()
    {
        $username = General::cleanInput('string', $_POST['username']);
        $password = General::cleanInput('string', $_POST['password']);

        $this->factory->getLoginObj()->check($username, $password);
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->factory->getLoginObj()->Logout();
        echo General::ref($this->factory->getLoginObj()->getIndexPage());
    }

    /**
     * @return string
     */
    public function defaultAction()
    {
        if (!$this->factory->getLoginObj()->isLoggedIn()) {
            echo $this->factory->getTwigAdminObj()->render(
                'login.html',
                array('user' => 'שם משתמש', 'pass' => 'סיסמה', 'logint' => 'כניסה', 'rememberme' => 'זכור אותי')
            );
        } else {
            echo General::ref($this->factory->getLoginObj()->getLogPage());
        }

    }

    /**
     * @return string
     */
    public function addQuestion()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();
        echo $this->factory->getQuestionsObj()->add();
    }

    /**
     * @return string
     */
    public function addUser()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();
        echo $this->factory->getTwigAdminObj()->display('add_user.html');
    }

    /**
     * @return string|void
     */
    public function addExecuteQuestion()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $question = General::cleanInput('string', $_POST['question']);
        $answers = General::cleanInput('array', $_POST['answer']);
        $this->factory->getQuestionsObj()->addExecute($question, $answers);
    }

    /**
     * @return string|void
     */
    public function addExecuteUser()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $user = General::cleanInput('string', $_POST['user_name']);
        $password = General::cleanInput('string', $_POST['user_password']);
        $email = General::cleanInput('email', $_POST['user_email']);
        $this->factory->getUsersObj()->addExecute($user, $password, $email);
    }

    /**
     * @return string
     */
    public function editQuestion()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        echo $this->factory->getQuestionsObj()->edit($id);
    }

    /**
     * @return string
     */
    public function editUser()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();
        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        echo $this->factory->getUsersObj()->edit($id);
    }

    /**
     * @return string|void
     */
    public function editExecuteQuestion()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $qid = General::cleanInput('int', $_POST['qid']);
        $question = General::cleanInput('string', $_POST['question']);
        $answers = General::cleanInput('array', $_POST['answer']);
        $this->factory->getQuestionsObj()->editExecute($qid, $question, $answers);
    }

    /**
     * @return string|void
     */
    public function editExecuteUser()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $id = General::cleanInput('int', $_POST['user_id']);
        $user = General::cleanInput('string', $_POST['user_name']);
        $password = General::cleanInput('password', $_POST['user_password']);
        $email = General::cleanInput('email', $_POST['user_email']);
        $this->factory->getUsersObj()->editExecute($id, $user, $password, $email);
    }

    /**
     * @return string|void
     */
    public function editExecuteSettings()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $settingsArr = General::cleanInput('array', $_POST['settings']);
        $this->factory->getSettingsObj()->editExecute($settingsArr);
    }

    /**
     * @return string
     */
    public function deleteQuestion()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        $this->factory->getQuestionsObj()->delete($id);
        echo General::messageSent(
            "The question and all it's answers were successfully deleted",
            'index.php?do=questions'
        );
    }

    /**
     * @return string
     */
    public function deleteUser()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        $this->factory->getUsersObj()->delete($id);
    }

    /**
     * @return string
     */
    public function deleteAnswer()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        $questionID = isset($_GET['questionID']) ? General::cleanInput('int', $_GET['questionID']) : null;
        $this->factory->getQuestionsObj()->deleteAnswer($id);
        echo General::messageSent(
            'Answer deleted successfully',
            'index.php?do=editQuestion&id=' . $questionID
        );
    }

    /**
     * @return string
     */
    public function answersShow()
    {
        $this->factory->getLoginObj()->checkIsLoggedIn();

        $qid = isset($_GET['qid']) ? General::cleanInput('int', $_GET['qid']) : null;
        $is_pie = isset($_GET['is_pie']) ? General::cleanInput('string', $_GET['is_pie']) : null;
        $this->factory->getQuestionsObj()->showAnswers($qid, $is_pie);
    }
}
