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
                <div class="board_title">
                    <!-- <h2>Board "<?= $board->title ?>"</h2> -->
                    <h2>Board ''code php titre''</h2>
                    <!-- code php
                    id board pour delete et edit (?) -->
                    <!-- <input type='text' name='id_board' value='<?= $board->id ?>' hidden> -->
                    <ul class="icons">
                        <li>
                            <form class='link' action='board/edit' method='post'>
                                <input type='text' name='id_board' value='php' hidden>
                                <input type='submit' value="&#xf044"class="fas fa-edit">
                            </form>
                        </li>
                        <li>
                            <form class='link' action='board/delete' method='post'>
                                <input type='text' name='id_board' value='php' hidden>
                                <input type='submit' value="&#xf2ed" class="far fa-trash-alt">
                            </form>
                        </li>
                    </ul>
                </div>    
                <p class="credit">Created 'code php time' ago by <strong>'code php fullname'</strong>. 'code php modified'</p>
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