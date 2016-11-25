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
    protected $database;

    /** @var  int */
    protected $settingsId;

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
     * @param DBInterface      $database
     * @param                  $settingsId
     */
    public function __construct(Twig_Environment $twig, DBInterface $database, $settingsId)
    {
        $this->database = $database;
        $this->twig = $twig;
        $this->settingsId = $settingsId;

        $settings = $this->processSettings($this->settingsId);
        $this->setProperties($settings);
    }

    /**
     * @param  int $sid
     *
     * @return boolean|array
     */
    private function checkSettingsExist($sid)
    {
        $queryResult = $this->database->getById('settings', $sid);
        if (empty($queryResult)) {return false;}
        return $queryResult;
    }

    /**
     * @param  int $sid
     *
     * @return string|array
     */
    private function processSettings($sid)
    {
        $settings = $this->checkSettingsExist($sid);
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
        $this->siteName = $queryResult['site_name'];
        $this->resultNumber = $queryResult['site_resultsnumber'];
        $this->siteCookies = $queryResult['site_cookies'];
        $this->siteCache = $queryResult['site_cache'];
        $this->siteMaxAnswers = $queryResult['site_maxanswers'];
    }

    /**
     * @return string
     */
    public function edit()
    {
        $settings = $this->processSettings($this->settingsId);
        return $this->twig->render('edit_settings.html', array(
            'id' => $settings['id'],
            'site_name' => $settings['site_name'],
            'site_resultsnumber' => $settings['site_resultsnumber'],
            'site_cookies' => $settings['site_cookies'],
            'site_cache' => $settings['site_cache'],
            'site_maxanswers' => $settings['site_maxanswers']
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
            $settings = $this->database->getById('settings', $this->settingsId, 'bean');
            $this->database->editRow($settings, array(
                'site_name' => $settingsArr['site_name'],
                'site_resultsnumber' => $settingsArr['site_resultsnumber'],
                'site_cookies' => $settingsArr['site_cookies'],
                'site_cache' => $settingsArr['site_cache'],
                'site_maxanswers' => $settingsArr['site_maxanswers']
            ));
            $this->database->store($settings);
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
