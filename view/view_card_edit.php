<!DOCTYPE html>
<html lang="fr">
<head>
    <?php $title="Card edit"; include('head.php'); ?>
    <script src = "lib/js/validation.js" type="text/javascript"></script>
    <script src = "lib/js/common.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            add_calendar_menu();
            setup_edit_card_validation();

            // reset la date côté client
            let reset_button = $('<button id="reset-dd" class="button">Reset due date</button>');

            reset_button.click(function(event) {
                event.preventDefault();
                //fonctionne que sur la valeur par défaut si l'id est spécifique, dans ma versionde chrome en tout cas...
                // cf. https://stackoverflow.com/questions/20885890/how-do-you-programmatically-clear-html5-date-fields
                $("input[type=date]").val("");
            });

            $("#reset-date-check-box").replaceWith(reset_button);

        })
    </script>
</head>

<body class="has-navbar-fixed-top m-4"">
        <header id="main_header">
            <?php include('menu.php') ?>
        </header>
        <?= $breadcrumb->get_trace() ?>
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

                    <form id="card-edit" action="card/edit" method="post">
                        <input id="card-id" type="text" name="card_id" value=<?= $card->get_id() ?> hidden>
                        <input id="board-id" type="text" name="board_id" value='<?= $card->get_board_id() ?>' hidden>
                        <input type="text" name="confirm" value="true" hidden>
                        <input type="text" name="redirect_url" value="<?= $redirect_url ?>" hidden>

                        <div class="field">
                            <label class="label">Title</label>
                            <div class="control">
                                <input id="card-title" class="input" type="text" name="card_title" value='<?= $card_title ?>' placeholder='<?= $card_title ?>'>
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
                                <input class="input" type ="text" name="board_title" value='<?= $card->get_board_title() ?>' disabled>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Column</label>
                            <div class="control">
                                <input class="input" type ="text" name="column_title" value='<?= $card->get_column_title() ?>' disabled>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-one-fifth">
                                <div class="field">
                                    <label class="label">Due Date</label>
                                    <div class="control">
                                        <input id="due-date" class="input" type="date" name="due_date" min="<?=ViewUtils::date_picker_min_due_date($card)?>" value="<?=ViewUtils::date_picker_due_date($due_date)?>">
                                    </div>
                                </div>
                                <div class="field mt-4">
                                    <label id="reset-date-check-box" class="checkbox">
                                        <input type="checkbox" name="reset_date">
                                        Reset due date
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="is-flex is-flex-direction-row mt-5 mb-5">
                            <a class="button is-light" href="card/view/<?= $card->get_id() ?>">Cancel</a>
                            <input class="button is-success ml-3" type='submit' value='Edit this card'>
                        </div>
                    </form>
                </section>
                <?php include("participants_section.php");
                      $redirect_url = "card/edit/". $card->get_id() . "#comments"; include('comments_section.php') ?>

            </article>
        </main>
    </body>
</html>
