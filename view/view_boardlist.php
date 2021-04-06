<!DOCTYPE html>
<html lang="fr">
<?php include('head.php'); ?>

<body class="has-navbar-fixed-top m-4">
        <header>
        <?php include('menu.php'); ?>
        </header>
        <?php if($user): ?>
        <main class="">
            <article class="mt-2 mb-5">
                <h2  class="title is-4">Your boards</h2>
                <div class="is-flex is-flex-direction-row is-align-items-center" >

                    <?php foreach($user->get_own_boards() as $board): ?>
                        <div class="card has-background-info mr-2">
                            <div class="card-content">
                                <a class="content has-text-white" href="board/view/<?= $board->get_id() ?>" >
                                    <b><?= $board->get_title() ?></b> <?= ViewUtils::get_columns_string($board->get_columns()) ?>
                                </a>
                            </div>
                        </div>
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
                <div class="is-flex is-flex-direction-row" >
                    <?php foreach($user->get_collaborating_boards() as $board): ?>
                    <div class="card has-background-success mr-2">
                        <div class="card-content">
                            <a class="content has-text-white" href="board/view/<?= $board->get_id() ?>"><b><?= $board->get_title() ?></b> <?= ViewUtils::get_columns_string($board->get_columns()) ?><br/>by <?= $board->get_owner_fullName() ?></a>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </article>
            <?php endif ?>

            <?php if ($user->is_admin()):; ?>
            <article>
                <h2  class="title is-4">Other's boards</h2>
                    <div class="is-flex is-flex-direction-row" >
                    <?php foreach($user->get_admin_visible_boards() as $board): ?>
                        <div class="card has-background-grey mr-2">
                            <div class="card-content">
                                <a class="content has-text-white" href="board/view/<?= $board->get_id() ?>"><b><?= $board->get_title() ?></b> <?= ViewUtils::get_columns_string($board->get_columns()) ?><br/>by <?= $board->get_owner_fullName() ?></a>
                                </a>
                            </div>
                        </div>
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