<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;


/**
 * Class Settings
 *
 * @package MyPoll\Classes
 */
class Settings
{
    /** @var  \Twig_Environment */
    public $twig;

    /**
     * @param object $twig
     */
    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function edit($id)
    {
        $settings = Facade::load('settings', $id);
        if (!$settings->isEmpty()) {
            return $this->twig->render('settings.html', array(
                'id' => $settings->id,
                'site_name' => $settings->site_name,
                'site_resultsnumber' => $settings->site_resultsnumber,
                'site_cookies' => $settings->site_cookies,
                'site_cache' => $settings->site_cache
            ));
        } else {
            return General::ref('index.php');
        }
    }

    /**
     * @param int $id
     * @param array $settingsArr
     *
     * @return void
     */
    public function editExecute($id, $settingsArr)
    {
        try {
            $settings = Facade::load('settings', $id);
            $settings->site_name = $settingsArr['site_name'];
            $settings->site_resultsnumber = $settingsArr['site_resultsnumber'];
            $settings->site_cookies = $settingsArr['site_cookies'];
            $settings->site_cache = $settingsArr['site_cache'];
            Facade::store($settings);

            echo "Settings edited successfully";
        } catch (Exception $e) {
            echo 'Error :' . $e->getMessage();
        }

    }

}
