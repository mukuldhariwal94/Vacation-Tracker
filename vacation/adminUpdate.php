<?php
/**
 * Created by PhpStorm.
 * User: is046231
 * Date: 10/1/16
 * Time: 6:24 PM
 */

spl_autoload_register(function ($class_name) {
    include "classes/" . $class_name . '.php';
});

session_start();


/**
 * Created by PhpStorm.
 * User: MD046225
 * Date: 10/2/2016
 * Time: 8:38 PM
 */
$assoc_id = $_POST["username"];
$manager_id = $_POST["manager"];
$message = 'Data Updated';
try {
    $dbh = new PDO('mysql:host=127.0.0.1:3306;dbname=cerner', "root", "");
    $sql = "UPDATE employee SET manager = '" . $manager_id . "' WHERE assoc_id='" . $assoc_id . "'" . '';
    $result = $dbh->exec($sql);

} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
echo "<script type='text/javascript'>alert('$message');</script>";
header("Location:admin.php");
?>