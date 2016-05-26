<?php namespace App\Utils;

use Hash;

/**
 * Utils
 */
class Utils
{
    /**
     * Get a value for an array safely
     *
     * @param $arr
     * @param $key
     * @return null
     */
    public static function safeGet($arr, $key) {
        if (!$arr || !$key) {
            return null;
        }
        if (is_array($arr)) {
            return isset($arr[$key])? $arr[$key] : null;
        }
        return null;
    }

    /**
     * Take a string_like_this and return a stringLikeThis
     *
     * @param string
     * @return string
     */
    public static function snakeToCamel($val) {
        $val = str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));
        $val = strtolower(substr($val,0,1)).substr($val,1);
        return $val;
    }

    /**
     * Generate password
     *
     * @param $email
     * @param $name
     * @return string
     */
    public static function generatePassword($email, $name) {
        return Hash::make(md5($email . $name));
    }

    /**
     * Check whether the password is a complex password or not
     *
     * @param $pwd
     * @return bool
     */
    public static function checkComplexPassword($pwd) {
        if (!$pwd) {
            return false;
        }

        if (preg_match('/^(?=.{8,}$)(?=.*[A-Za-z])(?=.*[0-9]).*/', $pwd)) {
            return true;
        }
        return false;
    }
}
