<!DOCTYPE html>
<html lang="fr">
<?php $title="Card edit"; include('head.php'); ?>

<body class="has-navbar-fixed-top m-4"">
        <header id="main_header">
            <?php include('menu.php') ?>
        </header>
        <?= $breadcrumb->get_trace(); ?>
        <main>
            <article>
                <header class="mb-5">
                    <h2 class="title">Edit a card</h2>
                    <p class="credit has-text-grey">Created <?= ViewUtils::created_intvl($card) ?> by <strong class="has-text-info">'<?= $card->get_author_fullName() ?>'</strong>. <?= ViewUtils::modified_intvl($card) ?>.</p>
                </header>
                <section>
                <?php if ($errors->has_errors()): ?>
                    <?php include('errors.php'); ?>
                <?php endif; ?>

                    <form action="card/edit" method="post">
                        <div class="field">
                            <label class="label">Title</label>
                            <div class="control">
                                <input class="input" type="text" name="card_title" value='<?= $card_title ?>' placeholder='<?= $card_title ?>'>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Body</label>
                            <div class="control">
                                <textarea class="textarea" name="body" rows="3"><?= $body ?></textarea>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Board</label>
                            <div class="control">
                                <input class="input" type ="text" name="title_board" value='<?= $card->get_board_title() ?>' disabled>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Column</label>
                            <div class="control">
                                <input class="input" type ="text" name="title_column" value='<?= $card->get_column_title() ?>' disabled>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-one-fifth">
                                <div class="field">
                                    <label class="label">Due Date</label>
                                    <div class="control">
                                        <input class="input" type="date" id="start" name="due_date" min="<?=ViewUtils::date_picker_min_due_date($card)?>" value="<?=ViewUtils::date_picker_due_date($due_date)?>">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="is-flex is-flex-direction-row mt-5 mb-5">
                            <a class="button is-light" href="card/view/<?= $card->get_id() ?>">Cancel</a>
                            <form action="card/edit" method="post">
                                <input type="text" name="id" value=<?= $card->get_id()?> hidden>
                                <input type="text" name="confirm" hidden>
                                <input type="text" name="redirect_url" value="<?= $redirect_url ?>" hidden>
                                <input class="button is-success ml-3" type='submit' value='Edit this card'>
                            </form>
                        </div>
                    </form>
                </section>
                <?php include("participants_section.php");
                      $redirect_url = "card/edit/". $card->get_id() . "#comments"; include('comments_section.php') ?>
            </article>
        </main>
    </body>
</html>
