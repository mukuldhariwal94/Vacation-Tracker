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

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin HomePage</title>
    <!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet"/>
    <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet"/>
    <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet"/>
    <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'/>
</head>
<body>
<div id="wrapper">
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="adjust-nav">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                </button>
                <img src="img/user.png" style="padding-left : 20px; padding-top:5px"/>
            </div>
            <span class="logout-spn">
                  <a href="logout.php" style="color:#fff;">LOGOUT</a>
                </span>
        </div>
    </div>
    <!-- /. NAV TOP  -->
    <nav class="navbar-default navbar-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="main-menu">
                <li class="active-link">
                    <a href="admin.html"><i class="fa fa-desktop "></i>Dashboard</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- /. NAV SIDE  -->
    <div id="page-wrapper">
        <div id="page-inner" class="container-fluid">
            <form class="form col-md-12 center-block" action="adminUpdate.php" method="post">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">User</label>
                        <div class="selectContainer">
                            <select class="form-control" name="username">
                                <option value="">Choose User</option>
                                <?php
                                $db = new DB();
                                $sql = "SELECT assoc_id from employee where assoc_id not in ('admin') and level not in ('2')";
                                $result = $db->select($sql);
                                $rows = array();
                                foreach ($result as $row) {
                                    echo '<option value="' . htmlspecialchars($row[0]) . '">'
                                        . htmlspecialchars($row[0])
                                        . '</option>';

                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Manager</label>
                        <div class="selectContainer">
                            <select class="form-control" name="manager">
                                <option value="">Choose to change Manager</option>
                                <?php
                                $db = new DB();
                                $sql = "SELECT assoc_id from employee where assoc_id not in ('admin') and level not in ('3')";
                                $result = $db->select($sql);
                                $rows = array();
                                foreach ($result as $row) {
                                    echo '<option value="' . htmlspecialchars($row[0]) . '">'
                                        . htmlspecialchars($row[0])
                                        . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="control-label"></label>
                    <div class="selectContainer">
                        <button class="btn btn-success" type="submit">Update Manager of Associate</button>
                    </div>
                </div>
            </form>

            <form class="form col-md-12 center-block" action="maxleavesUpdate.php" method="post">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Team Manager</label>
                        <div class="selectContainer">
                            <select class="form-control" name="manager">
                                <option value="">Choose Team Manger</option>
                                <?php
                                $db = new DB();
                                $sql = "SELECT assoc_id from employee where assoc_id not in ('admin') and level not in ('3')";
                                $result = $db->select($sql);
                                $rows = array();
                                foreach ($result as $row) {
                                    echo '<option value="' . htmlspecialchars($row[0]) . '">'
                                        . htmlspecialchars($row[0])
                                        . '</option>';

                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Max Number of leaves</label>
                        <div class="selectContainer">
                            <select class="form-control" name="leaves">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="control-label"></label>
                    <div class="selectContainer">
                        <button class="btn btn-success" type="submit">Update Max leaves for Managers Team</button>
                    </div>
                </div>
            </form>
            <!-- /. PAGE INNER  -->

            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
</div>
<div class="footer">
    <div class="row">
        <div class="col-lg-12">
            &copy; 2014 yourdomain.com | Design by: <a href="http://binarytheme.com" style="color:#fff;"
                                                       target="_blank">www.binarytheme.com</a>
        </div>
    </div>
</div>
<!-- /. WRAPPER  -->
<!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.10.2.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.min.js"></script>
<!-- CUSTOM SCRIPTS -->
<script src="assets/js/custom.js"></script>
</body>
</html>
