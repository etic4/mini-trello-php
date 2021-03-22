<!DOCTYPE html>
<html lang="fr">
<?php include('html_head.php'); ?>

<body class="home">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <main class="delete_confirm">
            <form id="main_form" action="<?= strtolower(get_class($instance)) ?>/remove" method="post">
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