<?php
/**
 * Created by PhpStorm.
 * User: is046231
 * Date: 10/1/16
 * Time: 2:21 PM
 */

session_start();

spl_autoload_register(function ($class_name) {
    include "classes/" . $class_name . '.php';
});

$type = $_POST['type'];
echo $type;
function debug_to_console($data)
{

    if (is_array($data))
        $output = "<script>console.log( 'Debug Objects: " . implode(',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}

//------------------- Code Starts ------------------------

$db = new DB();
$assoc_id = $_SESSION["assoc_id"];

if ($type == 'new') {
    //if a new holiday is dropped into the calendar

    $data = $db->select("Select * from employee WHERE assoc_id = '{$assoc_id}'");

    $getHolidays = "Select end_date, start_date from holiday where assoc_id = '{$assoc_id}' and status  = 0";
    $rows = $db->select($getHolidays);

    $pendingHolidays = 1;

    $ed = $sd = $days = $interval =  "";

    foreach ($rows as $row) {
        $ed = new DateTime($row["end_date"]);
        $sd = new DateTime($row["start_date"]);

        $interval = $ed->diff($sd);
        $pendingHolidays += $interval->format('%a');
    }

    $_SESSION['msg'] = $pendingHolidays;

    $pto = $data[0]["pto"];
    $pto_temp = $data[0]["temp_pto"];

    if($pto -  $pendingHolidays < 0){
        $_SESSION['error'] = "PTO Balance unavailable";
        return;
    }

    if ($pto_temp == 0) {
        $_SESSION["error"] = "PTO Balance unavailable";
        return false;
    }

    $startdate = $_POST['startdate'];
    $sd = explode("T", $startdate)[0];
    $title = $_POST['title'];

    $date = new DateTime($sd);
    $date->add(new DateInterval('P1D'));
    $end = $date->format('Y-m-d') . "\n";

    try {
        //add holiday row to db
        $sql = "INSERT INTO `holiday` (`assoc_id`, `start_date`, `end_date`, `status`) VALUES ( '" . $assoc_id . "', '" . $sd . "', '" . $end . "', '0')";
        $db->insert($sql);

        $query = "UPDATE employee SET temp_pto = pto - 1 WHERE assoc_id = '{$assoc_id}'";
        $db->update($query);

    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }

    $res = $db->select("Select max(`id`) as ID from holiday WHERE assoc_id = '{$assoc_id}'");

    echo json_encode(array('status' => 'success', 'eventid' => $res[0]["ID"]));

} else if ($type == 'resize') {
    //if an already existing holiday is re-sized

    $sign = "";
    $interval = "";
    $pendingHolidays = "";

    $startdate = $_POST['startdate'];
    $title = $_POST['title'];
    $enddate = $_POST['enddate'];

    $data = $db->select("Select * from employee e, holiday h WHERE e.assoc_id =  h.assoc_id AND h.id = {$title}");
    $pto = $data[0]["pto"];

    $prev_enddate = $data[0]["end_date"];


    $pto_temp = $data[0]["temp_pto"];

    $datetime1 = new DateTime($startdate);
    $datetime_selected = new DateTime($enddate);
    //$interval = $datetime1->diff($datetime2);

    $datetime_prev = new DateTime($prev_enddate);

    if($datetime_prev > $datetime_selected){
        $sign = "+";
        $interval = $datetime_prev->diff($datetime_selected);

    }else {
        $sign = "-";
        $interval = $datetime_selected->diff($datetime_prev);
    }

    $change = $interval->format('%a');
    //$_SESSION["msg"] = "first  ".$pendingHolidays.$sign;

    $getHolidays = "Select end_date, start_date from holiday where assoc_id = '{$assoc_id}' and status  = 0";
    $rows = $db->select($getHolidays);

    $_SESSION["msg"]  = "here";

    $ed = $sd = $days = $interval_t =  "";

    $_SESSION["msg"] = "here2";

    foreach ($rows as $row) {
        $ed = new DateTime($row["end_date"]);
        $sd = new DateTime($row["start_date"]);

        $interval_t = $ed->diff($sd);
        $pendingHolidays += $interval_t->format('%a');
    }

    $_SESSION["msg"] = "pd = ".$pendingHolidays.",\npto = ".$pto." ,\nsub ".($pto - $pendingHolidays - $change).$sign;

    if("-" == $sign){
        if ((($pto - $pendingHolidays )- $change) < 0){
            $_SESSION["error"] = "PTO Balance unavailable";
            return false;
        }
    }

    //$_SESSION['msg'] = "PD".$pendingHolidays;



    if ($pto_temp <= 0) {
        $_SESSION["error"] = "PTO Balance unavailable";
        return false;
    }

    $color = $_POST['color'];
    if ($color == '#5cb85c' or $color == '#d9534f') {
        echo json_encode(array('status' => 'unchanged'));
        return false;
    }

    //$_SESSION["msg"] = $interval->format('%a');

    $days = $interval->format('%a');
    var_dump($pto_temp . $days);

    if ($pto_temp <= 0) {
        $_SESSION["error"] = "PTO Balance unavailable";
        return false;
    } else {

        $sql = "UPDATE holiday SET end_date = '" . $enddate . "' WHERE id=" . $title;

        if("+" == $sign){
            $query = "UPDATE employee SET temp_pto = temp_pto + {$days} WHERE assoc_id = '{$assoc_id}'";
        }else  $query = "UPDATE employee SET temp_pto = temp_pto - {$days} WHERE assoc_id = '{$assoc_id}'";

        try {
            $db->update($sql);
            $db->update($query);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    if (count($result) > 0)
        echo json_encode(array('status' => 'success'));
    else
        echo json_encode(array('status' => 'failed'));

} else if ($type == 'approve') {
    //if an already existing holiday is approved by manager

    $data = $db->select("Select * from employee WHERE assoc_id = '{$assoc_id}'");

    $pto = $data[0]["pto"];
    $pto_temp = $data[0]["temp_pto"];

    $title = explode("-", $_POST['title'])[1];

    try {

        $query = "Select * from holiday h, employee e WHERE h.assoc_id = e.assoc_id AND h.id = {$title}";

        $result = $db->select($query)[0];

        debug_to_console($result);

        $start = $result["start_date"];
        $end = $result["end_date"];
        $empid = $result["assoc_id"];

        $datetime1 = new DateTime($start);
        $datetime2 = new DateTime($end);
        $interval = $datetime1->diff($datetime2);

        $days = $interval->format('%a');

        //set vacation status to 1 if if vacation is approved by manager
        $sql = "UPDATE `holiday` SET `status` = '1' WHERE `holiday`.`id` = " . $title;
        $db->update($sql);

        //decrease pto balance if vacation is rejected by manager
        $sql2 = "Update employee SET pto = pto - {$days} WHERE assoc_id = '{$empid}'";
        $db->update($sql2);

    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }

    echo json_encode(array('status' => 'success', 'eventid' => "{$title}"));

} else if ($type == 'reject') {
    //if an already existing holiday is rejected by manager

    $data = $db->select("Select * from employee WHERE assoc_id = '{$assoc_id}'");

    $pto = $data[0]["pto"];
    $pto_temp = $data[0]["temp_pto"];

    $title = explode("-", $_POST['title'])[1];

    debug_to_console($title);

    //Get data of employee wrt a particular holiday
    $select = "Select * from holiday h, employee e WHERE h.assoc_id = e.assoc_id AND h.id = {$title}";

    $result = $db->select($select)[0];

    debug_to_console($result);

    $start = $result["start_date"];
    $end = $result["end_date"];
    $empid = $result["assoc_id"];

    $datetime1 = new DateTime($start);
    $datetime2 = new DateTime($end);
    $interval = $datetime1->diff($datetime2);

    $days = $interval->format('%a');

    $query = "UPDATE `employee` SET temp_pto = pto WHERE assoc_id = '{$empid}'";

    //$_SESSION["msg"] = "hi-" . $days . "-days-" . $data[0]["temp_pto"] . "-finish-" . $query;

    try {

        //set vacation status to -1 if if vacation is rejected by manager
        $sql = "UPDATE `holiday` SET `status` = '-1' WHERE `holiday`.`id` = " . $title;
        $db->update($sql);

        //increase temp_pto balance if vacation is rejected by manager

        $db->update($query);

    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }

    echo json_encode(array('status' => 'success', 'eventid' => "{$title}"));

} else if ($type == 'resetdate') {

    $title = $_POST['title'];
    $startdate = $_POST['start'];
    $end = $_POST["end"];

    $data = $db->select("Select * from employee e, holiday h WHERE e.assoc_id =  h.assoc_id AND h.id = {$title}");

    $pto = $data[0]["pto"];
    $pto_temp = $data[0]["temp_pto"];

//    $startdate_db = $data[0]['start_date'];
//    $end_db = $data[0]["end_date"];
//
//    $datetime1 = new DateTime($startdate);
//    $datetime2 = new DateTime($startdate_db);
//
//
//
//    $_SESSION["msg"] = "nc";
//
//    if($datetime1 == $datetime2){
//        $_SESSION["msg"] = "hi"."true".$pto.$data[0];
//    }else
//    $_SESSION["msg"] = "hi"."flase".$pto.$data[0].$startdate_db.$startdate;

    $color = $_POST['color'];

    if ($color == '#5cb85c' or $color == '#d9534f') {
        echo json_encode(array('status' => 'unchanged'));
        return false;
    }

    try {

        $sql = "UPDATE `holiday` SET `start_date`='{$startdate}',`end_date`='{$end}' WHERE id = {$title}";
        $result = $db->update($sql);

    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }

    if (count($result) > 0)
        echo json_encode(array('status' => 'success'));
    else
        echo json_encode(array('status' => 'failed'));
} else if ($type == 'delete') {

    $data = $db->select("Select * from employee WHERE assoc_id = '{$assoc_id}'");

    $pto = $data[0]["pto"];
    $pto_temp = $data[0]["temp_pto"];

    $title = $_POST['title'];
    $startdate = $_POST['startdate'];
    $end = $_POST['enddate'];
    $color = $_POST['color'];
    $datetime1 = new DateTime($startdate);
    $datetime2 = new DateTime($end);
    $interval = $datetime1->diff($datetime2);

    $days = $interval->format('%a');
    try {
        $sql = "DELETE from `holiday` WHERE `start_date`='{$startdate}' AND  `end_date`='{$end}' AND assoc_id='{$assoc_id}'";
        $result = $db->update($sql);
        if ($color == '#5cb85c') {
            $sql2 = "Update employee SET pto = pto + {$days} WHERE assoc_id = '{$assoc_id}'";
            $db->update($sql2);
            $sql3 = "Update employee SET temp_pto = pto WHERE assoc_id = '{$assoc_id}'";
            $db->update($sql3);
        }
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
    if (count($result) > 0)
        echo json_encode(array('status' => 'success'));
    else
        echo json_encode(array('status' => 'failed'));
}
?>