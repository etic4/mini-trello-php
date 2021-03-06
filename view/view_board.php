<!DOCTYPE html>
<html lang="fr">
<head>
    <?php $title="Board"; include('head.php'); ?>
    <script src = "lib/js/validation.js" type="text/javascript"></script>
    <script src = "lib/js/delete-confirm.js" type="text/javascript"></script>
    <script src = "lib/js/drag-and-drop.js" type="text/javascript"></script>
    <script src = "lib/js/common.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            add_calendar_menu();
            setup_add_column_validation();
            setup_add_card_validation();
            setup_delete_confirm();
            setup_drag_and_drop();
        });
    </script>
</head>

<body class="has-navbar-fixed-top m-4">
	<header>
     <?php include('menu.php'); ?>
	</header>
    <?= $breadcrumb->get_trace(); ?>
	<main>
        <article>
            <header>
                <div class="is-flex is-flex-direction-row is-align-items-baseline">
                    <h2 class="title"><?= $board->get_title()?></h2>

                    <?php if ($user->is_owner($board) || $user->is_admin()): ?>
                    <a class="icon ml-5" href="board/edit/<?= $board->get_id() ?>">
                        <button class="button is-medium align-baseline is-white p-0" type="submit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </a>

                    <a class="button is-medium is-white p-0 ml-2" href="board/collaborators/<?= $board->get_id() ?>">
                        <i class="fas fa-users"></i>
                    </a>

                    <form id="board-delete-form" class="icon is-medium" action='board/delete' method='post'>
                        <input type='text' name='id' value='<?= $board->get_id() ?>' hidden>
                        <input type='text' name='confirm' hidden>
                        <button  id="board-delete" class="button align-baseline is-white p-0 ml-2" type="submit">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>

                    <?php endif; ?>
                </div>

                <div class="block has-text-grey mb-5">
                    Created <?= ViewUtils::created_intvl($board) ?> by <strong class="has-text-info">'<?= $board->get_owner_fullName() ?>'</strong>. <?= ViewUtils::modified_intvl($board) ?>.
                </div>
            </header>
            <div class="is-flex is-flex-direction-row is-align-items-start">
                <div id="board-droppable" class="is-flex is-flex-direction-row is-align-items-start pr-3">
                    <?php foreach($board->get_columns() as $column): ?>
                        <?php include("column.php"); ?>
                    <?php endforeach; ?>
                </div>
                <aside class="trello-add-column">
                    <form id="column-add" action="column/add" method="post">
                        <input id="board-id" type='text' name='board_id' value='<?= $board->get_id() ?>' hidden>
                        <div class="field has-addons">
                            <div class="control">
                                <input id="column-title" class="input" type="text" name="column_title" placeholder="Add a column">
                            </div>
                            <div class="control">
                                <button type="submit" class="button align-baseline is-info"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </form>
                    <?php if ($errors->has_errors("column", "add")): ?>
                        <?php include('errors.php'); ?>
                    <?php endif; ?>
                </aside>
            </div>
        </article>
    </main>
    <!--delete-confirm-->
    <?php include("delete_confirm_modal.php");?>
</body>
</html>