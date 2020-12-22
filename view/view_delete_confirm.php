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
    <body>
        <header>
            <?php include('menu.php'); ?>
        </header>
        <main class="delete_confirm">
            <!-- code php delete board, delete column, delete card -->
            <form action="<?= strtolower(get_class($instance)) ?>/delete" method="post">
                <h2><i class="far fa-trash-alt"></i></h2>
                <p>Are you sure ?</p>
                <hr>
                <p>Do you really want to delete this <?= strtolower(get_class($instance)) ?> ?</p>
                <?php if($cant_delete) :?>
                <p>You do not have the permission to delete this  <?= strtolower(get_class($instance)) ?> </p>  
                <?php else : ?>
                <p>This process cannot be undone.</p>
                <?php endif; ?>
                <input type="text" name="id" value=<?= $instance->get_id()?> hidden>
                <ul class="wrapper">
                    <li>
                        <input type='submit' value='Cancel' name='cancel'>
                    </li>
                    
                    <?php if(!($cant_delete)) :?>
                        <li>
                        <input type='submit' value='Delete' name='delete'>
                        </li>
                    <?php endif; ?>                        
                    
                </ul>
            </form>
        </main>
    </body>
</html>