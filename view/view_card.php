<!DOCTYPE html>
<html lang="fr">
<?php $title = "Card"; include('head.php'); ?>

    <body class="has-navbar-fixed-top m-4">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <?= $breadcrumb->get_trace(); ?>
        <main >
            <article id="viewCard">
                <header>
                    <div class="is-flex is-flex-direction-row is-align-items-baseline">
                        <h2 class="title">Card "<?= $card->get_title() ?>"</h2>
                        <a class="button  is-white is-medium p-0 ml-4" href="card/edit/<?= $card->get_id() ?>">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form class="icon ml-2" action='card/delete' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <button class="button is-medium is-white p-0" type="submit">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                    <div class="has-text-grey mb-1">
                        Created <?=ViewUtils::created_intvl($card) ?> by <strong>'<?= $card->get_author_fullName()?>'</strong>. <?= ViewUtils::modified_intvl($card) ?>
                    </div>
                    <div class="has-text-grey mb-5">
                        This card is on the board "<strong><?= $card->get_board_title() ?></strong>", column "<strong><?= $card->get_column_title() ?></strong>" at position <?= $card->get_position() ?>
                    </div>
                </header>
                    <section class="">
                        <p class="title is-4">Body</p>
                        <textarea class="textarea has-fixed-size has-text-black mb-4" disabled><?= $card->get_body() ?></textarea>

                        <? if($card->has_dueDate()): ?>
                        <p class="title is-4">Due date</p>
                        <div class="mb-4">
                            <?= ViewUtils::due_date_string($card->get_dueDate()) ?><
                        </div>

                        <?php else: ?>
                        <div class="mb-4">
                            This card has no due date yet.
                        </div>
                        <?php endif;?>

                        <?php if ($card->has_participants()): ?>
                            <p class="title is-4">Current Participant(s)</p>
                        <ul>
                            <?php foreach ($card->get_participants() as $participant): ?>
                                <li><?=$participant->get_fullName() ." (".$participant->get_email().")" ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <?php else: ?>
                        <div class="mb-4"
                            <p>This card has no participants yet</p>
                        </div>
                        <?php endif;?>

                    </section>

                    <?php if($card->has_comments()): ?>
                    <section>
                        <p class="title is-4">Comments</p>
                        <ul class="mb-5">
                            <?php foreach($card->get_comments() as $comment):?>
                                <?php include('comment.php'); ?>
                            <?php endforeach ?>
                        </ul>
                        <div class="columns">
                            <div class="column is-half">
                                <form class="" action="comment/add" method="post">
                                    <input type='text' name='card_id' value='<?= $card->get_id() ?>' hidden>
                                    <div class="field has-addons">
                                        <div class="control is-expanded">
                                            <input class="input" type="text" name="body" placeholder="Add a comment">
                                        </div>
                                        <div class="control">
                                            <button type="submit" class="button is-info">Add a comment</i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                    <?php endif;?>
            </article>
        </main>
    </body>
</html>