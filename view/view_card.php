<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
        <title>Card "<?= $card->get_title() ?>"</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body class="one_card">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <main>
            <article>
                <header>
                    <div class="title">
                        <h2>Card "<?= $card->get_title() ?>"</h2>
                        <ul class="icons">
                            <li>
                                <form class='link' action='card/edit' method='post'>
                                    <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                                    <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
                                </form>
                            </li>
                            <li>
                                <form class='link' action='card/delete' method='post'>
                                    <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                                    <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                                </form>
                            </li>
                        </ul>
                    </div>
                    <p class="credit">Created <?=DBTools::intvl($card->get_created_at(), new DateTime()); ?> by <strong>'<?= $card->get_author_name()?>'</strong>. <?= DBTools::laps($card->get_created_at(), $card->get_modified_at()); ?></p>
                    <p>This card is on the board "<strong><?= $board->get_title() ?></strong>", column "<strong><?= $column->get_title() ?></strong>" at position <?= $card->get_position() ?>.</p>
                </header>
                <div class="main_card">
                    <section class="display_card">
                        <h3>Body</h3>
                        <div>
                            <p><?= $card->get_body() ?></p>
                        </div>
                    </section>
                    <section class=comment>
                        <header>
                            <h3>Comments</h3>
                        </header>
                        <div class="main_comment">
                            <ul>
                                <?php foreach($card->get_comments() as $comment): ?>
                                <li class="display_one_comment">
                                    <p><?= $comment->get_body() ?></p>
                                    <p>by <strong><?= $comment->get_author_name() ?></strong> <?=DBTools::intvl($comment->get_createdAt(), new DateTime()); ?> ago.</p>
                                    <ul class="icons">
                                        <!-- si l'utilisateur est l'auteur du message -->
                                        <?php if($user == $comment->get_author()): ?>
                                        <li>
                                            <form class='link' action='comment/edit' method='post'>
                                                <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>
                                                <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
                                            </form>
                                        </li>
                                        <?php endif; ?>
                                        <!-- si l'utilisateur est proprio du tableau ou si l'utilisateur est l'auteur du message -->
                                        <?php if($user == $board->get_owner() || $user == $comment->get_author()): ?>
                                        <li>
                                            <form class='link' action='comment/delete' method='post'>
                                                <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>
                                                <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                                            </form>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                        <footer>
                            <form class="add" action="comment/add" method="post">
                                <input type="text" name="title">
                                <input type="submit" value="Add a comment">
                            </form>
                        </footer>
                    </section>
                </div>
            </article>
        </main>
    </body>
</html>