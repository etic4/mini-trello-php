<!DOCTYPE html>
<html lang="fr">
    <?php $title="Edit a column"; include('head.php'); ?>
    <body class="has-navbar-fixed-top m-4">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <?= $breadcrumb->get_trace(); ?>

        <main>
            <article>
                <header>
                    <h2 class="title">Edit title of column: "<?= $column->get_title() ?>"</h2>
                    <div class="columns">
                        <div class="column is-one-third">
                            <form action='column/edit' method='post'>
                                <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                                <input type="text" name="confirm" hidden>
                                <div class="field has-addons">
                                    <div class="control is-expanded">
                                        <input class="input" type="text" name="title" value="<?= $column->get_title() ?>">
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