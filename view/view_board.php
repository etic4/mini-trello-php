<!DOCTYPE html>
<html lang="fr">
<?php include('head.php'); ?>

<body class="has-navbar-fixed-top m-4">
	<header id="main_header">
     <?php include('menu.php'); ?>
	</header>
    <?= $breadcrumb->get_trace(); ?>
	<main>
        <article>
            <header>
                <div class="is-flex is-flex-direction-row">
                    <h2 class="title is-3"><?= $board->get_title()?></h2>
                    <?php if ($user->is_owner($board) || $user->is_admin()): ?>
                    <form action='board/edit/<?= $board->get_id() ?>' method='post'>
                        <input type='text' name='id' value='<?= $board->get_id() ?>' hidden>
<!--                        pas besoin d'instance, enlever partout -->
                        <input type='text' name='instance' value='board' hidden>
                        <button class="button is-white p-0 ml-2" type="submit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </form>
                    <form action='board/collaborators/<?= $board->get_id() ?>' method='post'>
                        <input type='text' name='id' value='<?= $board->get_id() ?>' hidden>
                        <button class="button is-white p-0 ml-2" type="submit">
                            <i class="fas fa-users"></i>
                        </button>
                    </form>

                    <form action='board/delete' method='post'>
                        <input type='text' name='id' value='<?= $board->get_id() ?>' hidden>
                        <button class="button is-white p-0 ml-2" type="submit">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <?php if ($errors->has_errors("board", "edit", $board->get_id())): ?>
                        <?php include('errors.php'); ?>
                    <?php endif; ?>
                </div>

                <div class="block mb-5">Created <?= ViewUtils::created_intvl($board) ?> by <strong>'<?= $board->get_owner_fullName() ?>'</strong>. <?= ViewUtils::modified_intvl($board) ?>.</div>
            </header>
            <div class="is-flex is-flex-direction-row is-align-items-start">
                <?php foreach($board->get_columns() as $column): ?>
                    <?php include("column.php"); ?>
                <?php endforeach; ?>
                <aside class="">
                    <form  action="column/add" method="post">
                        <input type='text' name='id' value='<?= $board->get_id() ?>' hidden>
                        <div class="field has-addons">
                            <div class="control">
                                <input class="input" type="text" name="title" placeholder="Add a column">
                            </div>
                            <div class="control">
                                <button type="submit" class="button is-info"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </form>
                    <?php if ($errors->has_errors()): ?>
                        <?php include('errors.php'); ?>
                    <?php endif; ?>
                </aside>     
            </div>
        </article>
    </main>
</body>
</html>