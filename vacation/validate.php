<?php
/**
 * Created by PhpStorm.
 * User: is046231
 * Date: 10/1/16
 * Time: 4:51 PM
 */

session_start();

spl_autoload_register(function ($class_name) {
    include "classes/" . $class_name . '.php';
});


$username = $_POST["username"];
$password = $_POST["password"];

$db = new DB();
$sql = "Select * FROM employee where assoc_id='" . $username . "' and password='" . $password . "'";

$result = $db->select($sql)[0];

var_dump($result);

if (count($result) == 0) {
    header("Location:index.html");
} else {
    if ($result["level"]==1) {
        echo $result;
        $_SESSION["name"] = $result["name"];
        $_SESSION["level"] = $result["level"];
        header("Location:admin.php");
    } else {
        $_SESSION["username"] = $username;
        $_SESSION["password"] = $password;
        $_SESSION["assoc_id"] = $result["assoc_id"];
        $_SESSION["name"] = $result["name"];
        $_SESSION["level"] = $result["level"];
        $_SESSION["error"] =null;
        $_SESSION["msg"] =null;
        header("Location:home.php");
    }
    //var_dump($_SESSION);
}
?>