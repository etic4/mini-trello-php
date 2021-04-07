<!DOCTYPE html>
<html lang="fr">
<?php $title="Board list"; include('head.php'); ?>

<body class="has-navbar-fixed-top m-4">
        <header>
        <?php include('menu.php'); ?>
        </header>
        <?php if($user): ?>
        <main class="">
            <article class="mt-2 mb-5">
                <h2  class="title is-4">Your boards</h2>
                <div class="is-flex is-flex-direction-row is-align-items-start" >
                    <?php foreach($user->get_own_boards() as $board): ?>
                    <a href="board/view/<?= $board->get_id() ?>">
                        <div class="card has-background-info has-text-white mr-2 p-4">
                            <b><?= $board->get_title() ?></b> <?= ViewUtils::get_columns_string($board->get_columns()) ?>
                        </div>
                    </a>
                    <?php endforeach; ?>

                    <form  action="board/add" method="post">
                        <div class="field has-addons">
                            <div class="control">
                                <input class="input" type="text" name="title" placeholder="Add a board">
                            </div>
                            <div class="control">
                                <button type="submit" class="button is-info"><i class="fas fa-sign-in-alt"></i></button>
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
                            <div class="card has-background-success has-text-white mr-2 p-4">
                                <b><?= $board->get_title() ?></b> <?= ViewUtils::get_columns_string($board->get_columns()) ?><br/>by <?= $board->get_owner_fullName() ?>
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
                            <div class="card has-background-grey has-text-white mr-2 p-4">
                                <b><?= $board->get_title() ?></b> <?= ViewUtils::get_columns_string($board->get_columns()) ?><br/>by <?= $board->get_owner_fullName() ?>
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