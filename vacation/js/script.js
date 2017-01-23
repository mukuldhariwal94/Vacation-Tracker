/**
 * Created by is046231 on 10/1/16.
 */

$("head").append('<script type="text/javascript" src="./lib/moment.min.js"></script>' +
    '<script type="text/javascript" src="./fullcalendar.js"></script>' +
    '');

$(document).ready(function () {
    $('.btn').click(function () {

        var id = this.id;



        if (id.indexOf("approve") != -1) {
            $.ajax({
                url: 'process.php',
                data: 'type=approve&title=' + id,
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    event.id = response.eventid;
                    $('#calendar').fullCalendar('updateEvent', event);
                    window.location.reload();
                },
                error: function (e) {
                    console.log(e.responseText);

                }
            });
            window.location.reload();
            $('#calendar').fullCalendar('updateEvent', event);
            console.log(event);
        }
        else if (id.indexOf("reject") != -1){
            $.ajax({
                url: 'process.php',
                data: 'type=reject&title=' + id,
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    event.id = response.eventid;
                    $('#calendar').fullCalendar('updateEvent', event);
                    window.location.reload();
                },
                error: function (e) {
                    console.log(e.responseText);

                }
            });
            window.location.reload();
            $('#calendar').fullCalendar('updateEvent', event);
            console.log(event);
        }

    });
});