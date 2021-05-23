<!DOCTYPE html>
<html lang="fr">
<head>
    <?php $title = "Card"; include('head.php'); ?>
    <script src = "lib/js/delete-confirm.js" type="text/javascript"></script>
    <script src = "lib/js/common.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            add_calendar_menu();
            setup_card_delete_confirm();
        });
    </script>
</head>
    <body class="has-navbar-fixed-top m-4">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <?= $breadcrumb->get_trace() ?>
        <main >
            <article>
                <header class="mb-5">
                    <div class="is-flex is-flex-direction-row is-align-items-baseline">
                        <h2 class="title">Card "<?= $card->get_title() ?>"</h2>

                        <a class="icon ml-1" href="card/edit/<?= $card->get_id() ?>/card_view_<?= $card->get_id() ?>">
                            <button class="button is-medium align-baseline is-white p-1" type="submit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </a>

                        <form id="card-delete-form" action='card/delete' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='text' name='confirm' hidden>
                            <button id="card-delete" class="button is-medium align-baseline is-white p-0 ml-2" type="submit">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                    <div class="has-text-grey mb-1">
                        Created <?= ViewUtils::created_intvl($card) ?> by <strong class="has-text-info">'<?= $card->get_author_fullName() ?>'</strong>. <?= ViewUtils::modified_intvl($card) ?>
                    </div>
                    <div class="has-text-grey ">
                        This card is on the board
                        <a href="board/view/<?= $card->get_board_id() ?>">"<strong class="has-text-info"><?= $card->get_board_title() ?></strong>"</a>,
                        column "<strong class="has-text-info"><?= $card->get_column_title() ?></strong>" at position <?= $card->get_position() ?>
                    </div>
                </header>
                <section>
                    <p class="title is-4 mb-2">Body</p>
                    <textarea class="textarea has-fixed-size has-text-black mb-4" disabled><?= $card->get_body() ?></textarea>

                    <?php if($card->has_dueDate()): ?>
                    <p class="title is-4 mb-2">Due date</p>
                    <div class="mb-4">
                        <?= ViewUtils::due_date_string($card->get_dueDate()) ?>
                    </div>

                    <?php else: ?>
                    <div class="mb-4">
                        <strong>This card has no due date yet.</strong>
                    </div>
                    <?php endif; ?>
                </section>

                <?php include("participants_section.php");
                    $redirect_url = "card/view/".$card->get_id()."#comments";
                    include("comments_section.php")
                ?>

            </article>
        </main>
        <!--delete-confirm-->
        <?php include("delete_confirm_modal.php");?>
    </body>
</html>