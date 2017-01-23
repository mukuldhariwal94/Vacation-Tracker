<?php
/**
 * Created by PhpStorm.
 * User: MD046225
 * Date: 10/16/2016
 * Time: 1:54 PM
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
$leaves = $_POST["leaves"];
$manager_id = $_POST["manager"];
$message = 'Data Updated';
try {
    var_dump($leaves);
    var_dump($manager_id);
    $dbh = new PDO('mysql:host=127.0.0.1:3306;dbname=cerner', "root", "");
    $sql = "UPDATE employee SET pto='" . $leaves . "' WHERE manager='" . $manager_id . "'" . '';

    $result = $dbh->exec($sql);
    $sql = "UPDATE employee SET temp_pto='" . $leaves . "' WHERE manager='" . $manager_id . "'" . '';
    $result = $dbh->exec($sql);

} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
echo "<script type='text/javascript'>alert('$message');</script>";
header("Location:admin.php");
?>