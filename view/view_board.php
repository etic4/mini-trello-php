<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="assets/logo.png" />
    <!-- code php
    récupère le titre du board et l'affiche ds le title -->
    <!-- <title>Boards <?= $board->title ?></title> -->
    <title>Boards php titre</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	<header>
     <?php include('menu.php'); ?>
	</header>
	<main class="board">
        <article>
            <header>
            <?php include("title.php"); ?>
            </header>
            <div class="column_display">  
                <?php include("columns.php"); ?>
                <aside class="column_form">
                    <form class="add" action="board/add_column" method="post">
                        <input type="text" name="title_column" placeholder="Add a column"/>
                        <input type="submit" value="&#xf067" class="fas fa-plus"/>
                    </form>
                </aside>
            </div>
        </article>
    </main>
</body>
</html>