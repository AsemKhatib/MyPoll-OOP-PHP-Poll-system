<?php

namespace MyPoll\Classes;

use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * Class Factory
 * @package MyPoll\Classes
 */
class Factory
{
    /** @var  Twig_Environment */
    protected $twigAdminObj;

    /** @var  Users */
    protected $usersObj;

    /** @var  Questions */
    protected $questionsObj;

    /** @var  Login */
    protected $loginObj;

    /** @var  Settings */
    protected $settingsObj;

    /** @var Pagenation  */
    protected $pagenationObj;

    /**
     * Factory constructor.
     * @param string $templatePathDir
     */
    public function __construct($templatePathDir)
    {
        $loaderAdmin = new Twig_Loader_Filesystem($templatePathDir);
        $this->twigAdminObj = new Twig_Environment($loaderAdmin, array());
        $this->settingsObj = new Settings($this, 1);
        $this->usersObj = new Users($this);
        $this->questionsObj = new Questions($this);
        $this->loginObj = new Login($this);
        $this->pagenationObj = new Pagenation();
    }

    /**
     * @return Login
     */
    public function getLoginObj()
    {
        return $this->loginObj;
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwigAdminObj()
    {
        return $this->twigAdminObj;
    }

    /**
     * @return Users
     */
    public function getUsersObj()
    {
        return $this->usersObj;
    }

    /**
     * @return Questions
     */
    public function getQuestionsObj()
    {
        return $this->questionsObj;
    }

    /**
     * @return Settings
     */
    public function getSettingsObj()
    {
        return $this->settingsObj;
    }

    /**
     * @return Pagenation
     */
    public function getPagenationObj()
    {
        return $this->pagenationObj;
    }
    
    
}