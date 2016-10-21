<?php

namespace MyPoll\Classes;

use Twig_Environment;
use MyPoll\Classes\Login\Login;

/**
 * Class AdminIndex
 *
 * @package MyPoll\Classes
 */

class AdminIndex
{
    /** @var Twig_Environment */
    protected $twig;

    /** @var Settings */
    protected $settings;

    /** @var Login */
    protected $login;

    /** @var Questions */
    protected $questions;

    /** @var Users */
    protected $users;

    /**
     * AdminIndex constructor.
     *
     * @param Twig_Environment $twig
     * @param Settings         $settings
     * @param Login            $login
     * @param Questions        $questions
     * @param Users            $users
     */
    public function __construct(
        Twig_Environment $twig,
        Settings $settings,
        Login $login,
        Questions $questions,
        Users $users
    ) {
        $this->twig = $twig;
        $this->settings = $settings;
        $this->login = $login;
        $this->questions = $questions;
        $this->users = $users;

        $this->twig->addGlobal('session', $_SESSION);
        $this->settings->checkCache();

        // add general settings to the global scope of Twig
        $this->twig->addGlobal('site_maxanswers', $this->settings->getSiteMaxAnswers());
        $this->twig->addGlobal('site_name', $this->settings->getSiteName());
    }

    /**
     * @return string
     */
    public function questions()
    {
        $this->login->checkIsLoggedIn();
        $startPage = isset($_GET['startPage']) ? General::cleanInput('int', $_GET['startPage']) : null;
        echo $this->questions->show($startPage);
    }

    /**
     * @return string
     */
    public function users()
    {
        $this->login->checkIsLoggedIn();
        $startPage = isset($_GET['startPage']) ? General::cleanInput('int', $_GET['startPage']) : null;
        echo $this->users->show($startPage);
    }

    /**
     * @return string
     */
    public function settings()
    {
        $this->login->checkIsLoggedIn();
        echo $this->settings->edit();
    }

    /**
     * @return void|string
     */
    public function check()
    {
        $username = General::cleanInput('string', $_POST['username']);
        $password = General::cleanInput('string', $_POST['password']);

        if (!$this->login->check($username, $password)) {
            echo General::messageSent('Wrong Username or Password', $this->settings->getIndexPage());
        } else {
            echo General::Ref($this->settings->getLogPage());
        }
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->login->Logout();
        echo General::ref($this->settings->getIndexPage());
    }

    /**
     * @return string
     */
    public function defaultAction()
    {
        if ($this->login->isLoggedIn()) {
            echo General::ref($this->settings->getLogPage());
        }

        echo $this->twig->render(
            'login.html',
            array('user' => 'שם משתמש', 'pass' => 'סיסמה', 'logint' => 'כניסה', 'rememberme' => 'זכור אותי')
        );
    }

    /**
     * @return string
     */
    public function addQuestion()
    {
        $this->login->checkIsLoggedIn();
        echo $this->questions->add();
    }

    /**
     * @return string
     */
    public function addUser()
    {
        $this->login->checkIsLoggedIn();
        echo $this->twig->display('add_user.html');
    }

    /**
     * @return string|void
     */
    public function addExecuteQuestion()
    {
        $this->login->checkIsLoggedIn();

        $question = General::cleanInput('string', $_POST['question']);
        $answers = General::cleanInput('array', $_POST['answer']);
        $this->questions->addExecute(array('question' => $question, 'answers' =>  $answers));
    }

    /**
     * @return string|void
     */
    public function addExecuteUser()
    {
        $this->login->checkIsLoggedIn();

        $user = General::cleanInput('string', $_POST['user_name']);
        $password = General::cleanInput('string', $_POST['user_password']);
        $email = General::cleanInput('email', $_POST['user_email']);
        $this->users->addExecute(array('user' => $user, 'password' => $password, 'email' => $email));
    }

    /**
     * @return string
     */
    public function editQuestion()
    {
        $this->login->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        echo $this->questions->edit($id);
    }

    /**
     * @return string
     */
    public function editUser()
    {
        $this->login->checkIsLoggedIn();
        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        echo $this->users->edit($id);
    }

    /**
     * @return string|void
     */
    public function editExecuteQuestion()
    {
        $this->login->checkIsLoggedIn();

        $qid = General::cleanInput('int', $_POST['qid']);
        $question = General::cleanInput('string', $_POST['question']);
        $answers = General::cleanInput('array', $_POST['answer']);
        $this->questions->editExecute(array('qid' => $qid, 'question' => $question, 'answers_old' => $answers));
    }

    /**
     * @return string|void
     */
    public function editExecuteUser()
    {
        $this->login->checkIsLoggedIn();

        $id = General::cleanInput('int', $_POST['user_id']);
        $user = General::cleanInput('string', $_POST['user_name']);
        $password = General::cleanInput('password', $_POST['user_password']);
        $email = General::cleanInput('email', $_POST['user_email']);
        $this->users->editExecute(array('id' => $id, 'user' => $user, 'password' => $password, 'email' => $email));
    }

    /**
     * @return string|void
     */
    public function editExecuteSettings()
    {
        $this->login->checkIsLoggedIn();

        $settingsArr = General::cleanInput('array', $_POST['settings']);
        $this->settings->editExecute($settingsArr);
    }

    /**
     * @return string
     */
    public function deleteQuestion()
    {
        $this->login->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        $this->questions->delete($id);
        echo General::messageSent(
            "The question and all it's answers were successfully deleted",
            $this->settings->getIndexPage() . '?do=questions'
        );
    }

    /**
     * @return string
     */
    public function deleteUser()
    {
        $this->login->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        $this->users->delete($id);
    }

    /**
     * @return string
     */
    public function deleteAnswer()
    {
        $this->login->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        $questionID = isset($_GET['questionID']) ? General::cleanInput('int', $_GET['questionID']) : null;
        $this->questions->deleteAnswer($id);
        echo General::messageSent(
            'Answer deleted successfully',
            $this->settings->getIndexPage() . '?do=editQuestion&id=' . $questionID
        );
    }

    /**
     * @return string
     */
    public function answersShow()
    {
        $this->login->checkIsLoggedIn();

        $qid = isset($_GET['qid']) ? General::cleanInput('int', $_GET['qid']) : null;
        $is_pie = isset($_GET['is_pie']) ? General::cleanInput('string', $_GET['is_pie']) : null;
        echo $this->questions->showAnswers($qid, $is_pie);
    }
}
