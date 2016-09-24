<?php

namespace MyPoll\Classes;

/**
 * Class General
 * @package MyPoll\Classes
 */

class General
{

    /**
     * @param array $arrays
     */
    public static function arrayAverage($arrays)
    {
        $myarr = $arrays;
        $sum = array_sum(array_map(function ($a) {
            return $a[1];
        }, $myarr));
        // walk through the array, print the percentage (value / sum) for each browser
        foreach ($myarr as $info) {
            echo round(($info[1] / $sum) * 100);
        }
        //return $result;
    }

    /**
     * @param $url
     *
     * @return string
     */
    public static function ref($url)
    {
        return '<meta http-equiv="refresh" content="0; url=' . $url . '">';
    }

    /**
     * @param string $msg
     * @param string $url
     *
     * @return string
     */
    public static function messageSent($msg, $url = "")
    {
        if (!empty($url)) {
            return '<meta http-equiv="refresh" content="2; url=' . $url . '">' . $msg;
        } else {
            return $msg;
        }
    }

    /**
     * @return string
     */
    public static function selfURL()
    {
        $s = empty($_SERVER["HTTPS"]) ? ''
            : ($_SERVER["HTTPS"] == "on") ? "s"
                : "";
        $protocol = self::strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? ""
            : (":" . $_SERVER["SERVER_PORT"]);

        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
    }

    /**
     * @param $s1
     * @param $s2
     *
     * @return string
     */
    public static function strLeft($s1, $s2)
    {
        return substr($s1, 0, strpos($s1, $s2));
    }

    /**
     * @param mixed $input
     * @param string $type
     *
     * @return string,int,array,bool
     */
    public static function cleanInput($type, $input)
    {
        switch ($type) {
            case 'string':
                return filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                break;
            case 'email':
                return filter_var($input, FILTER_VALIDATE_EMAIL);
                break;
            case 'array':
                return filter_var_array($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                break;
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'password':
                return filter_var($input);
                break;
        }

        return false;
    }
}
