<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;
use Exception;

/**
 * Class Settings
 *
 * @package MyPoll\Classes
 */
class Settings
{
    /** @var  Factory */
    protected $factory;

    /** @var  int */
    protected $id;

    /** @var  string */
    protected $siteName;

    /** @var  int */
    protected $resultNumber;

    /** @var  int */
    protected $siteCookies;

    /** @var  int */
    protected $siteCache;

    /** @var int */
    protected $siteMaxAnswers;

    /**
     * @return int
     */
    public function getSiteCache()
    {
        return $this->siteCache;
    }

    /**
     * @return int
     */
    public function getSiteCookies()
    {
        return $this->siteCookies;
    }

    /**
     * @return int
     */
    public function getResultNumber()
    {
        return $this->resultNumber;
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @return int
     */
    public function getSiteMaxAnswers()
    {
        return $this->siteMaxAnswers;
    }


    /**
     * @param Factory $factory
     * @param int $id
     */
    public function __construct($factory, $id)
    {
        $this->factory = $factory;
        $this->id = $id;
        $settings = Facade::load('settings', $this->id);
        $this->siteName = $settings->site_name;
        $this->resultNumber = $settings->site_resultsnumber;
        $this->siteCookies = $settings->site_cookies;
        $this->siteCache = $settings->site_cache;
        $this->siteMaxAnswers = $settings->site_maxanswers;
    }

    /**
     * @return string
     */
    public function edit()
    {
        $settings = Facade::load('settings', $this->id);
        if ($settings->isEmpty()) {
            return General::ref('index.php');
        }

        return $this->factory->getTwigAdminObj()->render('settings.html', array(
            'id' => $settings->id,
            'site_name' => $settings->site_name,
            'site_resultsnumber' => $settings->site_resultsnumber,
            'site_cookies' => $settings->site_cookies,
            'site_cache' => $settings->site_cache,
            'site_maxanswers' => $settings->site_maxanswers
        ));
    }

    /**
     * @param array $settingsArr
     *
     * @return void
     */
    public function editExecute($settingsArr)
    {
        try {
            $settings = Facade::load('settings', $this->id);
            $settings->site_name = $settingsArr['site_name'];
            $settings->site_resultsnumber = $settingsArr['site_resultsnumber'];
            $settings->site_cookies = $settingsArr['site_cookies'];
            $settings->site_cache = $settingsArr['site_cache'];
            $settings->site_maxanswers = $settingsArr['site_maxanswers'];
            Facade::store($settings);

            echo "Settings edited successfully";
        } catch (Exception $e) {
            echo 'Error :' . $e->getMessage();
        }

    }

    /**
     * @return void
     */
    public function checkCache()
    {
        if ($this->getSiteCache() == 1) {
            $this->factory->getTwigAdminObj()->setCache('../cache');
        }
    }
}
