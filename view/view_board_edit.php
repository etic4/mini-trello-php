<!DOCTYPE html>
<html lang="fr">
<head>
    <?php $title="Edit a board"; include('head.php'); ?>
    <script src = "lib/js/validation.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            setup_edit_board_validation();
        })
    </script>
</head>
    <body class="has-navbar-fixed-top m-4">
<!--        <script src="lib/js/board-validation.js" type="text/javascript"></script>-->

        <header>
            <?php include('menu.php'); ?>
        </header>
        <?= $breadcrumb->get_trace(); ?>
        <main>
            <article>
                <header>
                    <h2 class="title">Edit title of board: "<?= $board_title ?>"</h2>
                    <div class="columns">
                        <div class="column is-one-third">
                            <form id="board-edit" action='board/edit' method='post'>
                                <input id="board-id" type='text' name='id' value='<?= $id ?>' hidden>
                                <input type="text" name="confirm" hidden>

                                    <div class="field has-addons">
                                        <div class="control is-expanded">
                                            <input id="board-title" class="input" type="text" name="board_title" value="<?= $board_title ?>">
                                        </div>
                                        <div class="control">
                                            <button type="submit" class="button is-success"><i class="fas fa-check"></i></button>
                                        </div>
                                    </div>
                            </form>
                            <?php if ($errors->has_errors()): ?>
                                <?php include('errors.php'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </header>
            </article>
        </main>
    </body>
</html>