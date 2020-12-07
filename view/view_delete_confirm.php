<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="assets/logo.png" />
        <title>Delete</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <header>
            <?php include('menu.php'); ?>
        </header>
        <main class="delete_confirm">
            <!-- code php delete board, delete column, delete card -->
            <form action="card/delete" method="post">
                <h2><i class="far fa-trash-alt"></i></h2>
                <p>Are you sure ?</p>
                <hr>
                <p>Do you really want to delete this card ?</p>
                <p>This process cannot be undone.</p>
                <ul class="wrapper">
                    <li>
                        <input type='submit' value='Cancel'>
                    </li>
                    <li>
                        <input type="text" name="id" value='php code id_card' hidden>
                        <input type='submit' value='Delete'>
                    </li>
                </ul>
            </form>
        </main>
    </body>
</html>