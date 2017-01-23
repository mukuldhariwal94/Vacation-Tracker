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
<html>
<head>
    <meta charset='utf-8'/>

    <title>Home</title>

    <!-- Data Table -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.3.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

    <!-- General Functions -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.3.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Calendar -->
    <link href='fullcalendar.css' rel='stylesheet'/>
    <link href='fullcalendar.print.css' rel='stylesheet' media='print'/>
    <script src='lib/moment.min.js'></script>

    <script src='lib/jquery-ui.min.js'></script>
    <script src='fullcalendar.min.js'></script>

    <!-- Local CSS and JS -->
    <link href="css/mainView.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <script type="text/javascript" src="js/script.js"></script>

    <script>

        $(document).ready(function () {

            /* initialize the external events
             -----------------------------------------------------------------*/

            $('#external-events .fc-event').each(function () {

                // store data so the calendar knows to render an event upon drop
                $(this).data('event', {
                    title: $.trim($(this).text()), // use the element's text as the event title
                    stick: true // maintain when user navigates (see docs on the renderEvent method)
                });

                // make the event draggable using jQuery UI
                $(this).draggable({
                    zIndex: 999,
                    revert: true,      // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag
                });

            });


            /* initialize the calendar
             -----------------------------------------------------------------*/

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: true,
                weekends: false, // will hide Saturdays and Sundays
                droppable: true, // this allows things to be dropped onto the calendar
                drop: function () {
                    // is the "remove after drop" checkbox checked?
                    if ($('#drop-remove').is(':checked')) {
                        // if so, remove the element from the "Draggable Events" list
                        $(this).remove();
                    }
                },
                events: [

                    <?php

                    $id = $_SESSION["assoc_id"];

                    $sql = "SELECT * from holiday WHERE assoc_id = '" . $id . "'";

                    $db = new DB();

                    try {

                        $result = $db->select($sql);

                    } catch (PDOException $e) {
                        print "Error!: " . $e->getMessage() . "<br/>";
                        die();
                    }

                    $colour = "";
                    $arr = array();

                    foreach ($result as $row) {

                        if ($row["status"] == 1) {
                            $colour = "editable:false, color: '#5cb85c'}";
                        } elseif ($row["status"] == -1) {
                            $colour = "editable:false, color: '#d9534f'}";
                        } else $colour = "color: '#5bc0de'}";

                        $title = "{ title : '" . $row[0] . "',";
                        $start = "start : '" . explode(" ", $row[2])[0] . "',";
                        $end = "end : '" . explode(" ", $row[3])[0] . "',";

                        array_push($arr, $title . $start . $end . $colour);
                    }

                    echo join(",", $arr)
                    ?>
                ],
                eventReceive: function (event) {
                    var title = event.title;
                    var start = event.start.format("YYYY-MM-DD[T]HH:mm:SS");

                    $.ajax({
                        url: 'process.php',
                        data: 'type=new&title=' + title + '&startdate=' + start,
                        type: 'POST',
                        dataType: 'json',
                        success: function (response) {
                            event.id = response.eventid;
                            $('#calendar').fullCalendar('updateEvent', event);
                            window.location.reload();
                            $('#example').load('home.php');
                        },
                        error: function (e) {
                            console.log(e.responseText);

                        }
                    });
                    window.location.reload();
                    $('#calendar').fullCalendar('updateEvent', event);
                    console.log(event);
                },
                eventDrop: function (event, delta, revertFunc) {
                    var title = event.title;
                    var start = event.start.format();
                    var end = (event.end == null) ? start : event.end.format();
                    var color = event.color;

                    $.ajax({
                        url: 'process.php',
                        data: 'type=resetdate&title=' + title + '&start=' + start + '&end=' + end  + '&color=' + color,
                        type: 'POST',
                        dataType: 'json',
                        success: function (response) {
                            //if(response.status != 'success')
                            //revertFunc();
                            console.log("yes");
                        },
                        error: function (e) {
                            //revertFunc();
                            //alert('Error processing your request: '+e.responseText);
                            console.log(e.responseText);

                        }
                    });
                    window.location.reload();
                },
                eventResize: function (event, delta, revertFunc) {
                    var title = event.title;
                    var start = event.start.format();
                    var end = event.end.format();
                    var color = event.color;
                    $.ajax({
                        url: 'process.php',
                        data: 'type=resize&title=' + title + '&startdate=' + start + '&enddate=' + end + '&color=' + color,
                        type: 'POST',
                        dataType: 'json',
                        success: function (response) {
                            $('#calendar').fullCalendar('updateEvent', event);
                            window.location.reload();
                        },
                        error: function (e) {
                            console.log(e.responseText);
                        },
                        unchanged:function (response) {
                            alert("cannot chaneg already changed");
                        }

                    });
                    window.location.reload();
                    $('#calendar').fullCalendar('updateEvent', event);
                    console.log(event);
                },
                eventDragStop: function(event,jsEvent) {
                    var trashEl = jQuery('#calendarTrash');
                    var ofs = trashEl.offset();
                    var title = event.title;
                    var start = event.start.format();
                    var end = event.end.format();
                    var color = event.color;
                    var x1 = ofs.left;
                    var x2 = ofs.left + trashEl.outerWidth(true);
                    var y1 = ofs.top;
                    var y2 = ofs.top + trashEl.outerHeight(true);

                    if (jsEvent.pageX >= x1 && jsEvent.pageX<= x2 &&
                        jsEvent.pageY>= y1 && jsEvent.pageY <= y2) {
                        alert('Deleting Event');
                        $.ajax({
                            url: 'process.php',
                            data: 'type=delete&title=' + title + '&startdate=' + start + '&enddate=' + end + '&color=' + color,
                            type: 'POST',
                            dataType: 'json',
                            success: function (response) {
                                //event.id = response.eventid;
                                $('#calendar').fullCalendar('updateEvent', event);
                                window.location.reload();
                            },
                            error: function (e) {
                                console.log(e.responseText);
                            },
                            unchanged: function (response) {
                                alert('lol');
                            }

                        });
                    }
                    window.location.reload();
                }

                });


        });

        $(document).ready(function () {

            $('#calendar2').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar
                events: [
                    <?php

                    $id2 = $_SESSION["assoc_id"];

                    //View holidays taken by team associates
                    //view vacations taken by tram mates under same manager
                    //We have to get manager id in case of access level 3
                    if ($_SESSION["level"] == 3) {
                        $sql = "SELECT e.name, h.start_date, h.end_date  FROM `employee` e, holiday h WHERE e.assoc_id = h.assoc_id AND manager = (Select manager from employee where assoc_id = '{$id2}') AND status='1'";
                    } else
                        $sql = "SELECT e.name, h.start_date, h.end_date from holiday h, employee e WHERE h.assoc_id = e.assoc_id AND manager = '" . $id2 . "' AND status='1'";


                    $db = new DB();

                    try {
                        $result = $db->select($sql);
                    } catch (PDOException $e) {

                    }

                    $arr = array();
                    foreach ($result as $row) {

                        $title = "{ title : '" . $row[0] . "',";
                        $start = "start : '" . explode(" ", $row[1])[0] . "',";
                        $end = "end : '" . explode(" ", $row[2])[0] . "'}";
                        array_push($arr, $title . $start . $end);

                    }

                    echo join(",", $arr)
                    ?>

                ]

            });

            $('#tabs').tabs({
                activate: function(event, ui) {
                    $('#calendar2').fullCalendar('render');
                }
            });

        });

        $(document).ready(function () {

            $('#example').DataTable({
                "scrollY": "500px",
                "scrollCollapse": true,
                "paging": false
            });



        });

        $(document).ready(function () {
            $('#table2').DataTable();
        });


    </script>
    <style>

        body {
            text-align: center;
            font-size: 14px;
            font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
        }

        #wrap {
            width: 1100px;
            margin: 0 auto;
        }

        #external-events {
            float: left;
            width: 150px;
            padding: 0 10px;
            border: 1px solid #ccc;
            background: #eee;
            text-align: left;
        }

        #external-events h4 {
            font-size: 16px;
            margin-top: 0;
            padding-top: 1em;
        }

        #external-events .fc-event {
            margin: 10px 0;
            cursor: pointer;
        }

        #external-events p {
            margin: 1.5em 0;
            font-size: 11px;
            color: #666;
        }

        #external-events p input {
            margin: 0;
            vertical-align: middle;
        }

        #calendar {
            float: right;
            width: 900px;
        }

    </style>

</head>

<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
            <li><a href="#"><img src="img/user.png"></a></li>
            <li><a href="#" style="padding-top: 40px">

                    <?php

                    echo "" . $_SESSION["name"];
                    ?>


                    <a href="logout.php" style="color:black;">LOGOUT</a>

                </a></li>
        </ul>
    </div>
</nav>


<body>


<div class="container-fluid">

<div id="tabs">
    <ul class="nav nav-pills">
        <li class="active"><a data-toggle="tab" href="#home">Home</a></li>
        <li><a data-toggle="tab" href="#menu1">Previous Leaves</a></li>
        <li><a data-toggle="tab" href="#menu2">View Team</a></li>

        <?php


        if ($_SESSION["level"] <= 2) {
            $db = new DB();

            //SELECT * FROM holiday h , employee e WHERE h.assoc_id = e.assoc_id and status = 0 and e.manager = 'MM000000'
            //If manager of admin, show extra options
            $sql = "Select * from holiday h , employee e WHERE  h.assoc_id = e.assoc_id and h.status = 0 AND e.manager= '{$_SESSION["assoc_id"]}'";

            $result = count($db->select($sql));

            if ($result > 0) {
                echo '<li><a data-toggle="tab" href="#menu3">Requests
                    <span class="badge">' . $result . '</span>
                    </a></li>';
            } else {
                echo '<li><a data-toggle="tab" href="#menu3">Requests</a></li>';
            }


        }

        ?>

    </ul>

    <div class="tab-content">

        <div id="home" class="tab-pane fade in active">

            <p>Holidays taken by you</p>

            <hr/>

            <?php

            if ($_SESSION["msg"] != null) {
                $msg = "msg -> ".$_SESSION["msg"];
                echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
                $_SESSION["msg"] = null;
            }


            if ($_SESSION["error"] != null) {

                $msg = $_SESSION["error"];
                echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
                $_SESSION["error"] = null;
            }
            ?>

            <div id='wrap'>

                <div id='external-events'>
                    <h4>Vacation Events</h4>
                    <div class='fc-event'>Vacation</div>
                    <p>
                        <input type='checkbox' id='drop-remove'/>
                        <label for='drop-remove'>remove after drop</label>
                    </p>

                </div>



                <div id='calendar'></div>
                <div id="calendarTrash" class="calendar-trash"><img src="trash.png" style="padding: 20px"></div>

                <?php

                $sql = "SELECT pto from employee where assoc_id = '{$id}'";

                $db = new DB();

                $result = $db->select($sql);

                $pto = $result[0];

                echo "<div class=\"panel panel-primary\" style='width: 15%; float: left;'>
                    <div class=\"panel-heading\">
                        <h3 class=\"panel-title\">PTO Balance Left</h3>
                    </div>
                    <div class=\"panel-body\">
                     {$pto['pto']}
                    </div>
                  </div>";
                ?>

            </div>

        </div>

        <div id="menu1" class="tab-pane fade">

            <p>View your previous holidays</p>

            <hr/>

            <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>No. of Days</th>
                    <th>Duration of leaves</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                <?php

                $sql = "SELECT * 
                        from holiday h, employee e  
                        WHERE h.assoc_id = e.assoc_id and h.assoc_id = '" . $id . "'
                        ORDER BY h.start_date desc";

                $db = new DB();

                $result = $db->select($sql);

                $rows = array();

                foreach ($result as $row) {


                    $col3 = "    \n<td></td>";

                    $start = $row["start_date"];
                    $end = $row["end_date"];

                    $datetime1 = new DateTime($start);
                    $datetime2 = new DateTime($end);
                    $interval = $datetime1->diff($datetime2);

                    $days = $interval->format('%a day(s)');
                    $col1 = "    \n<td>" . $days . "</td>";


                    $col2 = "    \n<td>" . $datetime1->format('Y-m-d') . " - " .$datetime2->format('Y-m-d') . " </td>";

                    if ($row["status"] == "0") $col3 = "\n<td><div class=\"alert alert-info\" role=\"alert\">Pending " . $row["0"] . "</div></td>";
                    elseif ($row["status"] == "1") $col3 = "\n<td><div class=\"alert alert-success\" role=\"alert\">Approved " . $row["0"] . "</div></td>";
                    else $col3 = "\n<td><div class=\"alert alert-danger\" role=\"alert\">Rejected " . $row["0"] . "</div></td>";

                    array_push($rows, "\n<tr>" . $col1 . $col2 . $col3 . "\n</tr>");
                }

                foreach ($rows as $item)
                    echo $item

                ?>

                </tbody>
            </table>

        </div>

        <div id="menu2" class="tab-pane fade">

            <br/>

            <div id='wrap'>
                <div id='calendar2'></div>
            </div>
        </div>


        <?php
        if ($_SESSION["level"] <= 2) {
            include("menu3.html");
        }
        ?>


    </div>

</div>

</div>

<script>

</script>

</body>
</html>