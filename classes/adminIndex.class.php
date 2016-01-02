<?php

namespace MyPoll\Classes;

/**
 * Class AdminIndex
 * @package MyPoll\Classes
 */

class AdminIndex
{
    /** @var  object */
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
    }

    /**
     * @return string
     */
    public function questions()
    {
        $startPage = isset($_GET['startPage']) ? $_GET['startPage'] : '';

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
        $startPage = isset($_GET['startPage']) ? $_GET['startPage'] : '';

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
        $id = 1;

        if ($this->loginObj->isLoggedIn()) {
            echo $this->settingsObj->edit($id);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return void|string
     */
    public function check()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

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
        $question = $_POST['question'];
        $answer = $_POST['answer'];

        if ($this->loginObj->isLoggedIn()) {
            $this->questionsObj->addExecute($question, $answer);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string|void
     */
    public function addExecuteUser()
    {
        $user = $_POST['user_name'];
        $password = $_POST['user_password'];
        $email = $_POST['user_email'];

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
        $id = isset($_GET['id']) ? $_GET['id'] : '';

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
        $id = isset($_GET['id']) ? $_GET['id'] : '';

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
        $qid = $_POST['qid'];
        $question = $_POST['question'];
        $answer = $_POST['answer'];

        if ($this->loginObj->isLoggedIn()) {
            $this->questionsObj->editExecute($qid, $question, $answer);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }

    /**
     * @return string|void
     */
    public function editExecuteUser()
    {
        $id = $_POST['user_id'];
        $user = $_POST['user_name'];
        $password = $_POST['user_password'];
        $email = $_POST['user_email'];

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
        $settingsArr = $_POST['settings'];

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
        $id = isset($_GET['id']) ? $_GET['id'] : '';

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
        $id = isset($_GET['id']) ? $_GET['id'] : '';

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
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $questionID = isset($_GET['questionID']) ? $_GET['questionID'] : '';

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
        $qid = isset($_GET['qid']) ? $_GET['qid'] : '';
        $is_pie = isset($_GET['is_pie']) ? $_GET['is_pie'] : '';

        if ($this->loginObj->isLoggedIn()) {
            echo $this->questionsObj->showAnswers($qid, $is_pie);
        } else {
            echo General::ref($this->loginObj->getIndexPage());
        }
    }
}