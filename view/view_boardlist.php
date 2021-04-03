<!DOCTYPE html>
<html lang="fr">
<?php include('html_head.php'); ?>

<body class="home">
        <header id="main_header">
        <?php include('menu.php'); ?>
        </header>
        <?php if($user): ?>
        <main class="list">
            <article class="up" id="main_article">
                <h2>Your boards</h2>
                <div class="displayBoards">
                    <ul class="yourBoards">
                    <?php foreach($user->get_own_boards() as $board): ?>
                        <li><a href="board/board/<?= $board->get_id() ?>"><b><?= $board->get_title() ?></b> <?= ViewTools::get_columns_string($board->get_columns()) ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                    <form class="add" action="board/add" method="post">
                        <input type="text" name="title" placeholder="Add a board">
                        <input type="submit" value="&#xf067" class="fas fa-plus">
                        <?php if ($errors->has_errors()): ?>
                            <?php include('errors.php'); ?>
                        <?php endif; ?>
                    </form>
                </div>
            </article>

            <?php if ($user->has_collaborating_boards()): ?>
            <article class="down">
                <h2>Boards Shared with you</h2>
                <ul class="collabBoards">
                    <?php foreach($user->get_collaborating_boards() as $board): ?>
                        <li><a href="board/board/<?= $board->get_id() ?>"><b><?= $board->get_title() ?></b> <?= ViewTools::get_columns_string($board->get_columns()) ?><br/>by <?= $board->get_owner_fullName() ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <?php endif ?>

            <?php if ($user->is_admin()):; ?>
            <article class="others">
                <h2>Other's boards</h2>
                    <ul class="otherBoards">
                    <?php foreach($user->get_admin_visible_boards() as $board): ?>
                        <li><a href="board/board/<?= $board->get_id() ?>"><b><?= $board->get_title() ?></b> <?= ViewTools::get_columns_string($board->get_columns()) ?><br/>by <?= $board->get_owner_fullName() ?></a></li>
                    <?php endforeach; ?>
                    </ul>
            </article>
            <?php endif; ?>
        </main>
        <?php else:?>
        <main class="welcome">
            <p>Hello guest ! Please <a href="user/login">login</a> or <a href="user/signup">signup</a>.</p>
        </main>
        <?php endif;?>
    </body>
</html>