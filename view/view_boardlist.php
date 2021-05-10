<!DOCTYPE html>
<html lang="fr">
<head>
    <?php $title="Board list"; include('head.php'); ?>
    <script src = "lib/js/validation.js" type="text/javascript"></script>
    <script src = "lib/js/common.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            add_calendar_menu();
            setup_add_board_validation();
        })
    </script>
</head>
    <body class="has-navbar-fixed-top m-4">
<!--        <script src="lib/js/board-validation.js" type="text/javascript"></script>-->

        <header>
        <?php include('menu.php'); ?>
        </header>
        <?php if($user): ?>
        <main>
            <article class="mt-2 mb-5">
                <h2  class="title">Your boards</h2>
                <div class="is-flex is-flex-direction-row is-align-items-start" >
                    <?php foreach($user->get_own_boards() as $board): ?>
                    <a href="board/view/<?= $board->get_id() ?>">
                        <div class="card has-background-info has-text-white is-size-5 mr-2 p-4">
                            <b><?= $board->get_title() ?></b> <?= ViewUtils::columns_string($board->get_columns()) ?>
                        </div>
                    </a>
                    <?php endforeach; ?>

                    <form id="board-add"  action="board/add" method="post">
                        <div class="field has-addons">
                            <div class="control">
                                <input id="board-title" class="input" type="text" name="board_title" placeholder="Add a board">
                            </div>
                            <div class="control">
                                <button type="submit" class="button is-info"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <?php if ($errors->has_errors()): ?>
                            <?php include('errors.php'); ?>
                        <?php endif; ?>
                    </form>
                </div>
            </article>

            <?php if ($user->has_collaborating_boards()): ?>
            <article class="mb-5">
                <h2 class="title is-4">Boards Shared with you</h2>
                <div class="is-flex is-flex-direction-row is-align-items-start" >
                    <?php foreach($user->get_collaborating_boards() as $board): ?>
                        <a href="board/view/<?= $board->get_id() ?>">
                            <div class="card has-background-success has-text-white is-size-5 mr-2 p-4">
                                <b><?= $board->get_title() ?></b> <?= ViewUtils::columns_string($board->get_columns()) ?><br/>by <?= $board->get_owner_fullName() ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </article>
            <?php endif ?>

            <?php if ($user->is_admin()):; ?>
            <article>
                <h2  class="title is-4">Other's boards</h2>
                    <div class="is-flex is-flex-direction-row is-align-items-start" >
                    <?php foreach($user->get_admin_visible_boards() as $board): ?>
                        <a href="board/view/<?= $board->get_id() ?>">
                            <div class="card has-background-grey has-text-white is-size-5 mr-2 p-4">
                                <b><?= $board->get_title() ?></b> <?= ViewUtils::columns_string($board->get_columns()) ?><br/>by <?= $board->get_owner_fullName() ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    </div>
            </article>
            <?php endif; ?>
        </main>
        <?php else:?>
        <main>
            <p>Hello guest ! Please <a href="user/login">login</a> or <a href="user/signup">signup</a>.</p>
        </main>
        <?php endif;?>
    </body>
</html>