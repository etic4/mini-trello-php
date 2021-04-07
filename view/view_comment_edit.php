<!DOCTYPE html>
<html lang="fr">
    <?php $title="Edit a comment"; include('head.php'); ?>
    <body class="has-navbar-fixed-top m-4">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <?= $breadcrumb->get_trace(); ?>

        <main>
            <article>
                <header>
                    <h2 class="title">Edit comment</h2>
                    <div class="columns">
                        <div class="column is-half">
                            <form action='comment/edit/<?= $comment->get_id() ?>' method='post'>
                                <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>

                                    <div class="field has-addons">
                                        <div class="control is-expanded">
                                            <input class="input" type="text" name="body" value="<?= $comment->get_body() ?>">
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