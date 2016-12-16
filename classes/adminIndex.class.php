<?php

namespace MyPoll\Classes;

use MyPoll\Classes\Login\Login;
use Exception;
use Twig_Environment;

/**
 * Class AdminIndex
 *
 * @codeCoverageIgnore
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
     * @return void
     */
    public function show(FeaturesAbstract $abstract)
    {
        echo $this->login->checkIsNotLoggedIn();
        $startPage = isset($_GET['startPage']) ? General::cleanInput('int', $_GET['startPage']) : null;
        try {
            echo $abstract->show($startPage);
        } catch (Exception $e) {
            echo General::printException($e);
        }
    }

    /**
     * @return void
     */
    public function check()
    {
        $username = General::cleanInput('string', $_POST['username']);
        $password = General::cleanInput('string', $_POST['password']);

        if (!$this->login->check($username, $password)) {
            echo General::messageSent('Wrong Username or Password', $this->settings->getIndexPage());
        } else {
            echo General::ref($this->settings->getLogPage());
        }
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->login->logout();
        echo General::ref($this->settings->getIndexPage());
    }

    /**
     * @return void
     */
    public function defaultAction()
    {
        if ($this->login->isLoggedIn()) {
            echo General::ref($this->settings->getLogPage());
            exit;
        }

        echo $this->twig->render(
            'login.html',
            array('user' => 'שם משתמש', 'pass' => 'סיסמה', 'logint' => 'כניסה', 'rememberme' => 'זכור אותי')
        );
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return void
     */
    public function add(FeaturesAbstract $abstract)
    {
        echo $this->login->checkIsNotLoggedIn();
        try {
            echo $abstract->add();
        } catch (Exception $e) {
            echo General::printException($e);
        }
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return void
     */
    public function addExecute(FeaturesAbstract $abstract)
    {
        echo $this->login->checkIsNotLoggedIn();
        $requestArray = $abstract->getPostParamsForAddMethod();
        try {
            echo $abstract->addExecute($requestArray);
        } catch (Exception $e) {
            echo General::printException($e);
        }
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return void
     */
    public function edit(FeaturesAbstract $abstract)
    {
        echo $this->login->checkIsNotLoggedIn();
        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        try {
            echo $abstract->edit($id);
        } catch (Exception $e) {
            echo General::printException($e);
        }
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return void

     * @throws Exception
     */
    public function editExecute(FeaturesAbstract $abstract)
    {
        echo $this->login->checkIsNotLoggedIn();
        $requestArray = $abstract->getPostParamsForEditMethod();
        try {
            echo $abstract->editExecute($requestArray);
        } catch (Exception $e) {
            echo General::printException($e);
        }
    }

    /**
     * @param Settings $settings
     *
     * @return void
     */
    public function editSettings(Settings $settings)
    {
        echo $this->login->checkIsNotLoggedIn();
        echo $settings->edit();
    }

    /**
     * @param Settings $settings
     *
     * @return void
     */
    public function editExecuteSettings(Settings $settings)
    {
        echo $this->login->checkIsNotLoggedIn();
        $settingsArr = General::cleanInput('array', $_POST['settings']);
        try {
            echo $settings->editExecute($settingsArr);
        } catch (Exception $e) {
            echo General::printException($e);
        }
    }

    /**
     * @param FeaturesAbstract $abstract
     *
     * @return void
     */
    public function delete(FeaturesAbstract $abstract)
    {
        echo $this->login->checkIsNotLoggedIn();
        $id = isset($_GET['id']) ? General::cleanInput('int', $_GET['id']) : null;
        echo General::messageSent($abstract->delete($id), $this->settings->getIndexPage());
    }

    /**
     * @param Questions $questions
     *
     * @return void
     */
    public function deleteAnswer(Questions $questions)
    {
        echo $this->login->checkIsNotLoggedIn();

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
     * @return void
     */
    public function answersShow(Questions $questions)
    {
        echo $this->login->checkIsNotLoggedIn();
        $qid = isset($_GET['qid']) ? General::cleanInput('int', $_GET['qid']) : null;
        $is_pie = isset($_GET['is_pie']) ? General::cleanInput('string', $_GET['is_pie']) : null;
        echo $questions->showAnswers($qid, $is_pie);
    }
}
