<!DOCTYPE html>
<html lang="fr">
<head>
    <?php $title = "Delete Confirm"; include('head.php'); ?>
    <script src = "lib/js/common.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            add_calendar_menu();
        })
    </script>
</head>

    <body class="has-navbar-fixed-top m-4">
            <header>
                <?php include('menu.php'); ?>
            </header>
            <main>
                <?php include("delete_confirm.php"); ?>
            </main>
    </body>
</html>