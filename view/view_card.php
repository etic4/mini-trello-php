<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="assets/logo.png" />
    <title>Card</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
</head>
<body class="one_card">
	<header>
        <?php include('menu.php'); ?>
	</header>
    <main>
        <article>
            <header>
                <div class="title">
                    <h2>Card "<?= $card->title ?>"</h2>
                    <ul class="icons">
                        <li>
                            <form class='link' action='card/edit' method='post'>
                                <input type='text' name='id' value='<?= $card->id ?>' hidden>
                                <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
                            </form>
                        </li>
                        <li>
                            <form class='link' action='card/delete' method='post'>
                                <input type='text' name='id' value='<?= $card->id ?>' hidden>
                                <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                            </form>
                        </li>
                    </ul>
                </div>
                <p class="credit">Created 'code php time' ago by <strong>'<?= $card->author->fullname ?>'</strong>. 'code php modified'</p>
                <p>This card is on the board "<strong><?= $board->title ?></strong>", column "<strong><?= $column->title ?></strong>" at position <?= $card->position ?>.</p>
            </header>
            <div class="main_card">
                <section class="display_card">
                    <h2>Body</h2>
                    <div>
                        <p><?= $card->body ?></p>
                    </div>
                </section>
                <section class=comment>
                    <header>
                        <h2>Comments</h2>
                    </header>
                    <div class="main_comment">
                        <ul>
                            <?php foreach($comments as $comment): ?>
                            <li class="display_one_comment">
                                <p><?= $comment->body ?></p>
                                <p>by <strong><?= $comment->author ?></strong> php code time ago.</p>
                                <ul class="icons">
                                    <!-- si l'utilisateur est l'auteur du message -->
                                    <?php if($user == $comment->author): ?>
                                    <li>
                                        <form class='link' action='comment/edit' method='post'>
                                            <input type='text' name='id' value='<?= $comment->id ?>' hidden>
                                            <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
                                        </form>
                                    </li>
                                    <?php endif; ?>
                                    <!-- si l'utilisateur est proprio du tableau ou si l'utilisateur est l'auteur du message -->
                                    <?php if($user == $board->owner || $user == $comment->author): ?>
                                    <li>
                                        <form class='link' action='comment/delete' method='post'>
                                            <input type='text' name='id' value='<?= $comment->id ?>' hidden>
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