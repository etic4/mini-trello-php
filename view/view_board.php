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
                <div>
                    <?php if ($user->is_owner($board) || $user->is_admin()): ?>
                    <ul>
                        <li>
                            <form class='editTitle' action='board/edit/<?= $board->get_id() ?>' method='post'>
                                <input type='text' name='id' value='<?= $board->get_id() ?>' hidden>
                                <input type='text' name='instance' value='board' hidden>
                                <input type ="checkbox" id="toggle">
                                <label for="toggle"><i class="fas fa-edit"></i></label>
                                <input class="control" type="text" name="title" value="<?= $board->get_title() ?>">
                                <input class="fas fa-paper-plane" type="submit" value="&#xf1d8">
                                <button class="control"><i class="fas fa-arrow-left"></i></button>
                                <h2>Board "<?= $board->get_title() ?>"</h2>
                            </form>
                        </li>
                        <li>
                            <a href="board/collaborators/<?= $board->get_id() ?>"><i class="fa fa-users" aria-hidden="true"></i></a>
                        </li>
                        <li>
                            <form action='board/delete' method='post'>
                                <input type='text' name='id' value='<?= $board->get_id() ?>' hidden>
                                <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                            </form>
                        </li>
                    </ul>
                    <?php else: ?>
                        <h2>Board "<?= $board->get_title() ?>"</h2>
                    <?php endif; ?>
                    <?php if ($errors->has_errors("board", "edit", $board->get_id())): ?>
                        <?php include('errors.php'); ?>
                    <?php endif; ?>
                </div>
                <p>Created <?= ViewUtils::created_intvl($board) ?> by <strong>'<?= $board->get_owner_fullName() ?>'</strong>. <?= ViewUtils::modified_intvl($board) ?>.</p>
            </header>
            <div class="column_display">  
                <?php include("view_columns.php"); ?>
                <aside class="">
                    <form class="" action="column/add" method="post">
                        <input type='text' name='id' value='<?= $board->get_id() ?>' hidden>
                        <input type="text" name="title" placeholder="Add a column">
                        <input type="submit" value="&#xf067" class="fas fa-plus">
                    </form>
                    <?php if ($errors->has_errors("column", "add")): ?>
                        <?php include('errors.php'); ?>
                    <?php endif; ?>
                </aside>     
            </div>
        </article>
    </main>
</body>
</html>