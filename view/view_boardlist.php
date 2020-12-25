<!---->
<!DOCTYPE html>
<html lang="fr"><!---->
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
        <title>Boards</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body class="home">
        <header>
        <?php include('menu.php'); ?>
        </header>
        <?php if($user): ?>
        <main class="list">
            <article class="up">
                <h2>Your boards</h2>
                <div class="displayBoards">
                    <ul class="yourBoards">
                    <?php foreach($owners as $board): ?>
                        <li><a href="board/board/<?= $board['id'] ?>"><b><?= $board['title'] ?></b> <?= $board['columns'] ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                    <form class="add" action="board/add" method="post">
                        <input type="text" name="title" placeholder="Add a board">
                        <input type="submit" value="&#xf067" class="fas fa-plus">
                        <?php if (count($errors) != 0): ?>
                        <div class='errors'>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </article>
            <article class="down">
                <h2>Others' boards</h2>
                    <ul class="otherBoards">
                    <?php foreach($others as $board): ?>
                        <li><a href="board/board/<?= $board['id'] ?>"><b><?= $board['title'] ?></b><br/>by <?= $board['fullName'] ?></a></li>
                    <?php endforeach; ?>
                    </ul>
            </article>
        </main>
        <?php else:?>
        <main class="welcome">
            <p>Hello guest ! Please <a href="user/login">login</a> or <a href="user/signup">signup</a>.</p>
        </main>
        <?php endif;?>
    </body>
</html>