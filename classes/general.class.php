<?php

namespace MyPoll\Classes;

use Exception;

/**
 * Class General
 *
 * @package MyPoll\Classes
 */

class General
{

    /**
     * @param mixed $var
     *
     * @return bool
     */
    public static function issetAndNotEmpty($var)
    {
        if (isset($var) && !empty($var)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $url
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
    public static function messageSent($msg, $url = null)
    {
        if (!$msg) {return false;}
        if (!empty($url)) {
            return '<meta http-equiv="refresh" content="2; url=' . $url . '">' . $msg;
        }
        return $msg;
    }

    /**
     * @param mixed  $input
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
