<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
        <title>Delete</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body class="home">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <main class="delete_confirm">
            <form action="<?= strtolower(get_class($instance)) ?>/remove" method="post">
                <h2><i class="far fa-trash-alt"></i></h2>
                <p>Are you sure ?</p>
                <hr>
                <p>Do you really want to delete this <?= strtolower(get_class($instance)) ?> ?</p>
                <p>This process cannot be undone.</p>
                <input type="text" name="id" value=<?= $instance->get_id()?> hidden>
                <ul class="wrapper">
                    <li>
                        <input type='submit' value='Cancel' name='cancel'>
                    </li>
                    <li>
                        <input type='submit' value='Delete' name='delete'>
                    </li>
                </ul>
            </form>
        </main>
    </body>
</html>