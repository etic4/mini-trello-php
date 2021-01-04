<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
        <title>Edit a card</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body class="edit">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <main>
            <article>
                <header>
                    <h2>Edit a card</h2>
                    <p class="credit">Created <?=$card->get_created_intvl(); ?> by <strong>'<?= $card->get_author_name()?>'</strong>. <?= $card->get_modified_intvl(); ?></p>
                </header>
                <div class="main_card">
                    <form id="edit_card" action="card/update" method="post">
                        <div>
                            <label for="title" >Title</label>
                            <!-- value renvoie la valeur de départ si user ne modifie pas le titre -->
                            <input type="text" name="title" id="title" maxlength="128" value='<?= $card->get_title()?>' placeholder="code php title_card">
                        </div>
                        <div>
                            <label for="body">Body</label>
                            <textarea name="body" id="body" rows="10"><?= $card->get_body()    ?></textarea>
                        </div>
                        <div>
                            <label for="board">Board</label>
                            <input type ="text" name="title_board" id="title_board" value='<?= $board->get_title() ?>'  placeholder="php code title_board" disabled>
                        </div>
                        <div>
                            <label for="title_column">Column</label>
                            <input type ="text" name="title_column" id="title_column" value='<?= $column->get_title() ?>' placeholder="php code title_column" disabled>
                        </div>
                        <div>
                            <input type="text" name="id" value='<?= $card->get_id()?>' hidden>
                            <input type="submit" value="Cancel" form="edit_card" name="edit">
                            <input type="submit" value="Edit this card" form="edit_card" name="edit">
                        </div>
                    </form>
                </div>
                <?= include('comments.php'); ?>
            </article>
            
        </main>
    </body>
</html>