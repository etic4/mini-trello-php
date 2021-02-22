<!DOCTYPE html>
<html lang="fr"><!---->
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
    <title>Boards "<?= $board->get_title() ?>"</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
</head>
<body class="boardMain">
	<header id="main_header">
     <?php include('menu.php'); ?>
	</header>
	<main class="board">
        <article id="main_article">
            <header>
                <div class="title">
                    <?php if ($user->is_owner($board) || $user->is_admin()): ?>
                    <ul class="icons">
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
                        <li style="margin-right: 20px">
                            <a href="board/collaborators/<?= $board->get_id() ?>"><i class="fa fa-users" aria-hidden="true"></i></a>
                        </li>
                        <li>
                            <form class='link' action='board/delete' method='post'>
                                <input type='text' name='id' value='<?= $board->get_id() ?>' hidden>
                                <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                            </form>
                        </li>
                    </ul>
                    <?php endif; ?>
                    <?php if ($errors->has_errors("board", "edit", $board->get_id())): ?>
                        <?php include('errors.php'); ?>
                    <?php endif; ?>
                </div>
                <p class="credit">Created <?= $board->get_created_intvl() ?> by <strong>'<?= $board->get_owner_fullName() ?>'</strong>. <?= $board->get_modified_intvl() ?>.</p>
            </header>
            <div class="column_display">  
                <?php include("view_columns.php"); ?>
                <aside class="column_form">
                    <form class="add" action="column/add" method="post">
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