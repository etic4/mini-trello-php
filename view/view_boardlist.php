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
            <article class="up">
                <h2>Your boards</h2>
                <div class="displayBoards">
                    <ul class="yourBoards">
                    <?php foreach($yours as $your): ?>
                        <li><a href="board/board/<?= $your->id ?>"><b><?= $your->title ?></b></a></li>
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
                    <?php foreach($others as $other): ?>
                        <li><a href="board/board/<?= $other->id ?>"><b><?= $other->title ?></b><br/>by <?= $other->owner->fullname ?></a></li>
                    <?php endforeach; ?>
                    </ul>
            </article>
        </main>
        <footer>
        </footer>
    </body>
</html>