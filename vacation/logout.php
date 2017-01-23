<?php
/**
 * Created by PhpStorm.
 * User: MD046225
 * Date: 10/2/2016
 * Time: 5:52 PM
 */
$_SESSION = null;
$_POST = null;
session_destroy();
header("Location:index.html");