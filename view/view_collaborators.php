<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
    <title>Boards "<?= $board->get_title() ?>"</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <header id="main_header">
     <?php include("menu.php"); ?>
    </header>
    <?= $breadcrumb->get_trace(); ?>
    <main class="collab">
        <article>
            <header class="title">
                <h2><?= $board->get_title() ?> : Collaborators</h2>
            </header>
            <div class="main_collab>">
                <section class="current">
                    <h3>Current collaborator(s):</h3>
                    <ul id="collab_list">
                        <?php foreach ($board->get_collaborators() as $collaborator): ?>
                        <li>
                            <p><?=$collaborator?></p>
                            <form class='link' action='collaborator/remove' method='post'>
                                <input type='text' name='collab_id' value='<?= $collaborator->get_id() ?>' hidden>
                                <input type='text' name='board_id' value='<?= $board->get_id() ?>' hidden>
                                <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                            </form>
                        </li>
                    <?php endforeach ?>
                    </ul>
                </section>

                <?php if($board->has_user_not_collaborating()): ?>
                <section class="add_collab">
                    <h3>Add a new collaborator</h3>
                    <form class="add" action="collaborator/add" method="post">
                        <input type="text" name="board_id" value="<?= $board->get_id() ?>" hidden>
                        <select name="collab_id" id="others">
                            <?php foreach ($board->get_not_collaborating() as $collaborator): ?>
                                <option value="<?=$collaborator->get_id()?>"><?=$collaborator?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="submit" value="&#xf067" class="fas fa-plus">
                    </form>
                </section>
                <?php endif ?>
            </div>
        </article>
    </main>
</body>