<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
        <title>Edit a card</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body class="edit">
        <header>
            <?php include('menu.php'); ?>
        </header>
        <main>
            <article>
                <header>
                    <h2>Edit a card</h2>
                    <p class="credit">Created by 'code php fullname' 'code php time' ago . Modified 'code php modified' ago</p>
                </header>
                <div class="main_card">
                    <form id="edit_card" action="card/edit" method="post">
                        <div>
                            <label for="title" >Title</label>
                            <!-- value renvoie la valeur de départ si user ne modifie pas le titre -->
                            <input type="text" name="title" id="title" maxlength="128" value="code php title_card" placeholder="code php title_card">
                        </div>
                        <div>
                            <label for="body">Body</label>
                            <textarea name="body" rows="10">code php body</textarea>
                        </div>
                        <div>
                            <label for="board">Board</label>
                            <input type ="text" name="title_board" id="title_board" value="php code title_board" placeholder="php code title_board" disabled>
                        </div>
                        <div>
                            <label for="title_column">Column</label>
                            <input type ="text" name="title_column" id="title_column" value="php code title_column" placeholder="php code title_column" disabled>
                        </div>
                        <div>
                            <input type="submit" value="Cancel" form="edit_card">
                            <input type="submit" value="Edit this card" form="edit_card">
                        </div>
                    </form>
                </div>
                <footer>
                    
                </footer>
            </article>
        </main>
    </body>
</html>