<!DOCTYPE html>
<html lang="fr">
<head>
    <?php $title="Edit a column"; include('head.php'); ?>
    <script src = "lib/js/validation.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            setup_edit_column_validation();
        })
    </script>
</head>
    <body class="has-navbar-fixed-top m-4">
<!--        <script src="lib/js/column-validation.js" type="application/javascript"></script>-->
        <header>
            <?php include('menu.php'); ?>
        </header>
        <?= $breadcrumb->get_trace(); ?>

        <main>
            <article>
                <header>
                    <h2 class="title">Edit title of column: "<?= $column_title ?>"</h2>
                    <div class="columns">
                        <div class="column is-one-third">
                            <form id="column-edit" action='column/edit' method='post'>
                                <input type='text' name='id' value='<?= $column_id ?>' hidden>
                                <input id="column-id" type='text' name='column_id' value='<?= $column_id ?>' hidden>
                                <input id="board-id" type='text' name='board_id' value='<?= $board_id ?>' hidden>
                                <input type="text" name="confirm" hidden>
                                <div class="field has-addons">
                                    <div class="control is-expanded">
                                        <input id="column-title" class="input" type="text" name="column_title" value="<?= $column_title ?>">
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