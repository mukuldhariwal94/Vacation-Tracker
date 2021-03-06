<!--<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>

</body>
</html>

-->

<div id="menu3" class="tab-pane fade">
    <h3>Requests</h3>
    <p>Reject/Approve requests taken by team.</p>

    <table id="table2" class="cell-border" cellspacing="0" width="100%" style="padding-top: 10px">
        <thead>
        <tr>
            <th>Name</th>
            <th>Duration of leaves</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>

        <?php

            if ($_SESSION["level"] <= 2) {

                $db = new  DB();

                $sql = "SELECT * from employee e, holiday h WHERE e.assoc_id = h.assoc_id AND manager = '" . $_SESSION["assoc_id"] . "'";

                $result = $db->select($sql);

        $rows = array();

        foreach ($result as $emp) {
        $col1 = "<td>" . $emp["name"] . "</td>";
        $col2 = "<td>" . $emp["start_date"] . " - " . $emp["end_date"] . "</td>";
        $col3 = "<td></td>";

        if ($emp["status"] == "0") $col3 = "<td>Pending</td>";
        elseif ($emp["status"] == "1") $col3 = "<td>Approved</td>";
        else $col3 = "<td>Rejected</td>";

        if($emp["status"] == "0"){
        $col4 = "<td>
            <button type=\"button\" class=\"btn btn-primary\" id='approve-".$emp["id"]."'>Approve</button>
            <button type=\"button\" class=\"btn btn-warning\" id='reject-".$emp["id"]."'>Reject</button>
        </td>";
        }
        else $col4 = "<td>NA</td>";



        array_push($rows, "<tr>" . $col1 . $col2 . $col3 . $col4 . "</tr>");
        }

        foreach ($rows as $item) echo $item;
        }


        ?>
        </tbody>
    </table>

</div>
