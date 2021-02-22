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
<main>
    <article>
        <header>
            <h2>Board "<?= $board->get_title() ?>"</h2>
        </header>
        <div>
            <p>Current collaborator(s)</p>
            <ul>
                <?php foreach ($board->get_collaborators() as $collaborator): ?>
                <li class="flex-row"><?=$collaborator?>
                    <form id="collaborator-remove" action="collaborators/remove"  method="post">
                        <input type='text' name='id' value='<?= $collaborator->get_id() ?>' hidden>
                        <input type="text" name="board-id" value="<?=$board->get_id()?>" hidden>
                        <input type='submit' value="&#xf2ed" class="far fa-trash">
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php if($board->has_user_not_collaborating()): ?>
        <div>
            <label for="collaborator-add">Add a participant</label>
            <form id="collaborator-add" action="collaborator/add" method="post">
                <select name="id" id="collaborator-select">
                    <?php foreach ($board->get_not_collaborating() as $collaborator): ?>
                        <option value="<?=$collaborator->get_id()?>"><?=$collaborator?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="board-id" value="<?=$board->get_id()?>" hidden>
                <input type="submit" class="fas fa-plus" aria-hidden="true" value="">
            </form>
        </div>
        <?php endif ?>
    </article>
</main>
</body>
</html>