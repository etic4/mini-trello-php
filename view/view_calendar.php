<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php $title = "Calendar"; include('head.php'); ?>
        <link rel='stylesheet' type='text/css' href='lib/vendor/fullcalendar-5.6.0/fullcalendar.min.css' />
        <script type='text/javascript' src='lib/vendor/fullcalendar-5.6.0/fullcalendar.min.js'></script>
        <script type='text/javascript' src='lib/js/calendar.js'></script>
        <script type='text/javascript' src='lib/js/common.js'></script>
        <script>
            $(document).ready(function() {
                add_calendar_menu();
                setup_calendar();

                console.log("ok");
            });
        </script>

    </head>
    <body class="has-navbar-fixed-top m-4">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <main>
            <div id="boards_list" class="is-flex is-flex-direction-row p-2 mt-3 mb-3">
            </div>
            <div id="calendar"></div>
        </main>
    </body>
</html>
