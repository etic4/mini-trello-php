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
                    <!-- code php -> boucle foreach yourBoards
                    id récupéré ds le lien + titre -->
                    <ul class="yourBoards">
                        <li><a href="board/board/code php id"><b>code php titre</b></a></li>
                    </ul>
                    <form class="add" action="board/add_board" method="post">
                        <input type="text" name="new_board" placeholder="Add a board"/>
                        <!--<label for="addBoard">Add a board</label>-->
                        <input type="submit" value="xx"/>
                    </form>
                </div>
            </article>
            <article class="down">
                <h2>Others' boards</h2>
                    <!-- code php -> boucle foreach otherBoards
                    id récupéré ds le lien + titre + author-->
                    <ul class="otherBoards">
                        <li><a href="board/board/code php id"><b>code php titre</b><br/>by code php author</a></li>
                    </ul>
            </article>
        </main>
        <footer>
        </footer>
    </body>
</html>