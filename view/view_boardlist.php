<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
        <title>Boards</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <header>
        <?php include('menu.php'); ?>
        </header>
        <main class="list">
            <?php if($user): ?>;
            <article class="up">
                <h2>Your boards</h2>
                <div class="displayBoards">
                    <ul class="yourBoards">
                    <?php foreach($owners as $board): ?>
                        <li><a href="board/board/<?= $board['id'] ?>"><b><?= $board['title'] ?></b></a></li>
                    <?php endforeach; ?>
                    </ul>
                    <form class="add" action="board/add" method="post">
                        <input type="text" name="title" placeholder="Add a board">
                        <input type="submit" value="&#xf067" class="fas fa-plus">
                    </form>
                </div>
            </article>
            <article class="down">
                <h2>Others' boards</h2>
                    <ul class="otherBoards">
                    <?php foreach($others as $board): ?>
                        <li><a href="board/board/<?= $board['id'] ?>"><b><?= $board['title'] ?></b><br/>by <?= $board['fullname'] ?></a></li>
                    <?php endforeach; ?>
                    </ul>
            </article>
            <?php else:?>
            <p>Hello guest ! Please <a href="user/login">login</a> or <a href="user/signup">signup</a>.</p>
            <?php endif;?>
        </main>
    </body>
</html>