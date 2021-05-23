<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php $title="Edit a column"; include('head.php'); ?>
        <script src = "lib/js/common.js" type="text/javascript"></script>
        <script src = "lib/js/delete-confirm.js" type="text/javascript"></script>
        <script>
            $(document).ready(function() {
                add_calendar_menu();
                setup_collab_delete_confirm();
            })
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
                <h2 class="title mb-5"><?= $board->get_title() ?> : Collaborators</h2>
            </header>
            <?php if($board->has_collaborators()): ?>
            <section class="mb-5">
                <p class="title is-4">Current collaborator(s):</p>
                <ul>
                    <?php foreach ($board->get_collaborators() as $collaborator): ?>
                        <?php include("collaborator.php"); ?>
                    <?php endforeach ?>
                </ul>
            </section>
            <?php endif ?>

            <?php if($board->has_user_not_collaborating()): ?>
            <section>
                <p class="title is-4">Add a new collaborator</p>

                <form class="add" action="collaboration/add" method="post">
                    <input type="text" name="board_id" value="<?= $board->get_id() ?>" hidden>

                    <div class="field has-addons">
                        <div class="control">
                            <div class="select" >
                                <select name="collab_id" id="others">
                                    <?php foreach ($board->get_not_collaborating() as $collaborator): ?>
                                        <option value="<?=$collaborator->get_id()?>"><?=$collaborator?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="control">
                            <button type="submit" class="button is-info">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </section>
            <?php endif ?>
        </article>
    </main>
    <!--delete-confirm-->
    <?php include("delete_confirm_modal.php");?>
</body>