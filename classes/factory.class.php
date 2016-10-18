<?php

namespace MyPoll\Classes;

use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Class Factory
 *
 * @package MyPoll\Classes
 */
class Factory
{
    /** @var  mixed */
    protected $db;

    /** @var  Twig_Environment */
    protected $twigAdminObj;

    /** @var  Users */
    protected $usersObj;

    /** @var  Questions */
    protected $questionsObj;

    /** @var  Login */
    protected $loginObj;

    /** @var Cookie */
    protected $cookieObj;

    /** @var  Settings */
    protected $settingsObj;

    /** @var Pagination */
    protected $paginationObj;

    /**
     * Factory constructor.
     *
     * @param mixed $db
     * @param string $templatePathDir
     * @param int $settingsId
     */
    public function __construct($db, $templatePathDir, $settingsId)
    {
        $this->db = $db;
        $loaderAdmin = new Twig_Loader_Filesystem($templatePathDir);
        $this->twigAdminObj = new Twig_Environment($loaderAdmin, array());
        $this->paginationObj = new Pagination($this->getDb());
        $this->settingsObj = new Settings($this, $settingsId);
        $this->usersObj = new Users($this);
        $this->questionsObj = new Questions($this);
        $this->loginObj = new Login($this);
        $this->cookieObj = new Cookie();
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return Cookie
     */
    public function getCookieObj()
    {
        return $this->cookieObj;
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
     * @return Pagination
     */
    public function getPaginationObj()
    {
        return $this->paginationObj;
    }


}