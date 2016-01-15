<?php

namespace MyPoll\Classes;

/**
 * Class AdminIndex
 * @package MyPoll\Classes
 */

class AdminIndex
{
    /** @var  \Twig_Environment */
    protected $twigAdminObj;

    /** @var  object */
    protected $usersObj;

    /** @var  object */
    protected $questionsObj;

    /** @var  object */
    protected $loginObj;

    /** @var  object */
    protected $settingsObj;

    /**
     * @param $twigAdmin
     */
    public function __construct($twigAdmin)
    {
        $this->twigAdminObj = $twigAdmin;
        $this->settingsObj = new Settings($this->twigAdminObj, 1);
        $this->usersObj = new Users($this->twigAdminObj, $this->settingsObj);
        $this->questionsObj = new Questions($this->twigAdminObj, $this->settingsObj);
        $this->loginObj = new Login($this->settingsObj);

        // add general settings to the global scope of Twig
        $this->twigAdminObj->addGlobal('site_maxanswers', $this->settingsObj->getSiteMaxAnswers());
        $this->twigAdminObj->addGlobal('site_name', $this->settingsObj->getSiteName());
    }

    /**
     * @return string
     */
    public function questions()
    {
        $startPage = isset($_GET['startPage']) ? General::cleanInput('int', $_GET['startPage']) : null;

        if ($this->loginObj->isLoggedIn()) {
            echo $this->questionsObj->show($startPage);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string
     */
    public function users()
    {
        $startPage = isset($_GET['startPage']) ? General::cleanInput('int', $_GET['startPage']) : null;

        if ($this->loginObj->isLoggedIn()) {
            echo $this->usersObj->show($startPage);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string
     */
    public function settings()
    {
        if ($this->loginObj->isLoggedIn()) {
            echo $this->settingsObj->edit();
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return void|string
     */
    public function check()
    {
        $username = General::cleanInput('string', $_POST['username']);
        $password = General::cleanInput('string', $_POST['password']);

        $this->loginObj->check($username, $password);
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->loginObj->Logout();
        echo General::ref($this->loginObj->getIndexPage());
    }

    /**
     * @return string
     */
    public function defaultAction()
    {
        if ($this->loginObj->isLoggedIn()) {
            echo General::ref($this->loginObj->getLogPage());
        } else {
            echo $this->twigAdminObj->render(
                'login.html',
                array('user' => 'שם משתמש', 'pass' => 'סיסמה', 'logint' => 'כניסה')
            );
        }
    }

    /**
     * @return string
     */
    public function addQuestion()
    {
        if ($this->loginObj->isLoggedIn()) {
            echo $this->twigAdminObj->display('add_poll.html');
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string
     */
    public function addUser()
    {
        if ($this->loginObj->isLoggedIn()) {
            echo $this->twigAdminObj->display('add_user.html');
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string|void
     */
    public function addExecuteQuestion()
    {
        $question = General::cleanInput('string', $_POST['question']);
        $answers = General::cleanInput('array', $_POST['answer']);

        if ($this->loginObj->isLoggedIn()) {
            $this->questionsObj->addExecute($question, $answers);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string|void
     */
    public function addExecuteUser()
    {
        $user = General::cleanInput('string', $_POST['user_name']);
        $password = General::cleanInput('string', $_POST['user_password']);
        $email = General::cleanInput('email', $_POST['user_email']);

        if ($this->loginObj->isLoggedIn()) {
            $this->usersObj->addExecute($user, $password, $email);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string
     */
    public function editQuestion()
    {
        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;

        if ($this->loginObj->isLoggedIn()) {
            echo $this->questionsObj->edit($id);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string
     */
    public function editUser()
    {
        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;

        if ($this->loginObj->isLoggedIn()) {
            echo $this->usersObj->edit($id);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string|void
     */
    public function editExecuteQuestion()
    {
        $qid = General::cleanInput('int', $_POST['qid']);
        $question = General::cleanInput('string', $_POST['question']);
        $answers = General::cleanInput('array', $_POST['answer']);

        if ($this->loginObj->isLoggedIn()) {
            $this->questionsObj->editExecute($qid, $question, $answers);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string|void
     */
    public function editExecuteUser()
    {
        $id = General::cleanInput('int', $_POST['user_id']);
        $user = General::cleanInput('string', $_POST['user_name']);
        $password = General::cleanInput('password', $_POST['user_password']);
        $email = General::cleanInput('email', $_POST['user_email']);

        if ($this->loginObj->isLoggedIn()) {
            $this->usersObj->editExecute($id, $user, $password, $email);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string|void
     */
    public function editExecuteSettings()
    {
        $settingsArr = General::cleanInput('array', $_POST['settings']);

        if ($this->loginObj->isLoggedIn()) {
            $this->settingsObj->editExecute($settingsArr);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string
     */
    public function deleteQuestion()
    {
        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;

        if ($this->loginObj->isLoggedIn()) {
            echo $this->questionsObj->delete($id);
            echo General::messageSent(
                "The question and all it's answers were successfully deleted",
                'index.php?do=questions'
            );
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string
     */
    public function deleteUser()
    {
        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;

        if ($this->loginObj->isLoggedIn()) {
            echo $this->usersObj->delete($id);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string
     */
    public function deleteAnswer()
    {
        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        $questionID = isset($_GET['questionID']) ? General::cleanInput('int', $_GET['questionID']) : null;

        if ($this->loginObj->isLoggedIn()) {
            echo $this->questionsObj->deleteAnswer($id);
            echo General::messageSent(
                'Answer deleted successfully',
                'index.php?do=editQuestion&id=' . $questionID
            );
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string
     */
    public function answersShow()
    {
        $qid = isset($_GET['qid']) ? General::cleanInput('int', $_GET['qid']) : null;
        $is_pie = isset($_GET['is_pie']) ? General::cleanInput('string', $_GET['is_pie']) : null;

        if ($this->loginObj->isLoggedIn()) {
            echo $this->questionsObj->showAnswers($qid, $is_pie);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }
}
