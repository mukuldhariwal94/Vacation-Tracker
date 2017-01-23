<?php

/**
 * Created by PhpStorm.
 * User: is046231
 * Date: 9/30/16
 * Time: 9:10 PM
 */
class Config
{

    // property declaration
    public static $config = array(
        "dbname" => "cerner",
        "user" => "root",
        "pass" => "",
        "a");

// method declaration
    public static function getConfig()
    {
        return self::$config;
    }
}

?>