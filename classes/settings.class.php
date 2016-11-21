<?php

namespace MyPoll\Classes;

use MyPoll\Classes\Database\DBInterface;
use Exception;
use Twig_Environment;

/**
 * Class Settings
 *
 * @package MyPoll\Classes
 */
class Settings
{
    /** @var Twig_Environment */
    protected $twig;

    /** @var  DBInterface */
    protected $db;

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

    /** @var string */
    private $logPage = 'index.php?do=show&route=questions';

    /** @var string */
    private $indexPage = 'index.php';

    /**
     * @return string
     */
    public function getIndexPage()
    {
        return $this->indexPage;
    }

    /**
     * @return string
     */
    public function getLogPage()
    {
        return $this->logPage;
    }

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
     * Settings constructor.
     *
     * @param Twig_Environment $twig
     * @param DBInterface      $db
     * @param                  $id
     */
    public function __construct(Twig_Environment $twig, DBInterface $db, $id)
    {
        $this->db = $db;
        $this->twig = $twig;
        $this->id = $id;

        $settings = $this->processSettings($this->id);
        $this->setProperties($settings);
    }

    /**
     * @param  int $id
     *
     * @return boolean|array
     */
    private function checkSettingsExist($id)
    {
        $queryResult = $this->db->getById('settings', $id);
        if ($queryResult[0]->isEmpty()) {
            return false;
        }
        return $queryResult;
    }

    /**
     * @param  int $id
     *
     * @return string|array
     */
    private function processSettings($id)
    {
        $settings = $this->checkSettingsExist($id);
        if (!$settings) {
            echo General::ref($this->getIndexPage());
        }
        return $settings;
    }

    /**
     * @param array $queryResult
     *
     * @return void
     */
    private function setProperties($queryResult)
    {
        $queryResult = $queryResult[0];
        $this->siteName = $queryResult->site_name;
        $this->resultNumber = $queryResult->site_resultsnumber;
        $this->siteCookies = $queryResult->site_cookies;
        $this->siteCache = $queryResult->site_cache;
        $this->siteMaxAnswers = $queryResult->site_maxanswers;
    }

    /**
     * @return string
     */
    public function edit()
    {
        $settings = $this->processSettings($this->id);
        $settings = $settings[0];

        return $this->twig->render('edit_settings.html', array(
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
            $settings = $this->db->getById('settings', $this->id);
            $this->db->editRow($settings, array(
                'site_name' => $settingsArr['site_name'],
                'site_resultsnumber' => $settingsArr['site_resultsnumber'],
                'site_cookies' => $settingsArr['site_cookies'],
                'site_cache' => $settingsArr['site_cache'],
                'site_maxanswers' => $settingsArr['site_maxanswers']
            ));
            $this->db->store($settings);
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
            $this->twig->setCache('../cache');
        }
    }
}
