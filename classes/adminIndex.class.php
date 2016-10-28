<?php

namespace MyPoll\Classes;

use MyPoll\Classes\Login\Login;
use Twig_Environment;

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

    /**
     * AdminIndex constructor.
     *
     * @param Twig_Environment $twig
     * @param Settings         $settings
     * @param Login            $login
     */
    public function __construct(Twig_Environment $twig, Settings $settings, Login $login)
    {
        $this->twig = $twig;
        $this->settings = $settings;
        $this->login = $login;

        $this->twig->addGlobal('session', $_SESSION);
        $this->settings->checkCache();

        // add general settings to the global scope of Twig
        $this->twig->addGlobal('site_maxanswers', $this->settings->getSiteMaxAnswers());
        $this->twig->addGlobal('site_name', $this->settings->getSiteName());
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return string
     */
    public function show(FeaturesAbstract $abstract)
    {
        $this->login->checkIsLoggedIn();
        $startPage = isset($_GET['startPage']) ? General::cleanInput('int', $_GET['startPage']) : null;
        echo $abstract->show($startPage);
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
     * @param FeaturesAbstract $abstract
     *
     * @return string
     */
    public function add(FeaturesAbstract $abstract)
    {
        $this->login->checkIsLoggedIn();
        echo $abstract->add();
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return void
     */
    public function addExecute(FeaturesAbstract $abstract)
    {
        $this->login->checkIsLoggedIn();
        $requestArray = $abstract->getPostParamsForAddMethod();
        $abstract->addExecute($requestArray);
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return string
     */
    public function edit(FeaturesAbstract $abstract)
    {
        $this->login->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        echo $abstract->edit($id);
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return string|void
     */
    public function editExecute(FeaturesAbstract $abstract)
    {
        $this->login->checkIsLoggedIn();
        $requestArray = $abstract->getPostParamsForEditMethod();
        $abstract->editExecute($requestArray);
    }

    /**
     * @param Settings $settings
     *
     * @return string
     */
    public function editSettings(Settings $settings)
    {
        $this->login->checkIsLoggedIn();
        echo $settings->edit();
    }

    /**
     * @param Settings $settings
     *
     * @return string|void
     */
    public function editExecuteSettings(Settings $settings)
    {
        $this->login->checkIsLoggedIn();

        $settingsArr = General::cleanInput('array', $_POST['settings']);
        $settings->editExecute($settingsArr);
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return string
     */
    public function delete(FeaturesAbstract $abstract)
    {
        $this->login->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        $abstract->delete($id);
    }

    /**
     * @param Questions $questions
     *
     * @return string
     */
    public function deleteAnswer(Questions $questions)
    {
        $this->login->checkIsLoggedIn();

        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        $questionID = isset($_GET['questionID']) ? General::cleanInput('int', $_GET['questionID']) : null;
        $questions->deleteAnswer($id);
        echo General::messageSent(
            'Answer deleted successfully',
            $this->settings->getIndexPage() . '?do=edit&route=questions&id=' . $questionID
        );
    }

    /**
     * @param Questions $questions
     *
     * @return string
     */
    public function answersShow(Questions $questions)
    {
        $this->login->checkIsLoggedIn();

        $qid = isset($_GET['qid']) ? General::cleanInput('int', $_GET['qid']) : null;
        $is_pie = isset($_GET['is_pie']) ? General::cleanInput('string', $_GET['is_pie']) : null;
        echo $questions->showAnswers($qid, $is_pie);
    }
}
