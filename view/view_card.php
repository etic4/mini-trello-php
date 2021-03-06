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
        <header id="main_header">
            <?php include('menu.php'); ?>
        </header>
        <?= $breadcrumb->get_trace(); ?>
        <main >
            <article id="viewCard">
                <header>
                    <div class="title">
                        <h2>Card "<?= $card->get_title() ?>"</h2>
                        <ul class="icons">
                            <li>
                                <form class='link' action='card/edit_link' method='post'>
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
                    <p class="credit">Created <?=$card->get_created_intvl() ?> by <strong>'<?= $card->get_author_name()?>'</strong>. <?= $card->get_modified_intvl() ?>.</p>
                    <p>This card is on the board "<strong><?= $card->get_board_title() ?></strong>", column "<strong><?= $card->get_column_title() ?></strong>" at position <?= $card->get_position() ?>.</p>
                </header>
                <div class="main_card">
                    <?php if ($errors->has_errors()): ?>
                        <?php include('errors.php'); ?>
                    <?php endif; ?>
                    <section class="display_card">
                        <h3>Body</h3>
                        <div class="encadre">
                            <p><?= $card->get_body() ?></p>
                        </div>

                        <? if($card->has_dueDate()): ?>
                        <h3>Due date</h3>
                        <div class="simple-text">
                            <p><?= ViewTools::due_date_string($card->get_dueDate()) ?></p>
                        </div>

                        <?php else: ?>
                        <div class="simple-text">
                            <p>This card has no due date yet.</p>
                        </div>

                        <?php endif;?>

                        <?php if ($card->has_participants()): ?>
                            <h3>Current Participant(s)</h3>
                        <ul>
                            <?php foreach ($card->get_participants() as $participant): ?>
                                <li><?=$participant->get_fullName() ." (".$participant->get_email().")" ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <?php else: ?>
                        <div>
                            <p>This card has no participants yet</p>
                        </div>
                        <?php endif;?>

                    </section>
                    <?php include('view_comments.php'); ?>
                </div>
            </article>
        </main>
    </body>
</html>