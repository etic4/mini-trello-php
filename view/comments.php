<section class=comment>
    <header>
        <h3>Comments</h3>
    </header>
    <div class="main_comment">
        <ul>
            <?php foreach($card->get_comments() as $comment): ?>
            <li class="display_one_comment">
                <p><?= $comment->get_body() ?></p>
                <p>by <strong><?= $comment->get_author_name() ?></strong> <?=DBTools::intvl($comment->get_createdAt(), new DateTime()); ?> ago.</p>
                <ul class="icons">
                    <!-- si l'utilisateur est l'auteur du message -->
                    <?php if($user == $comment->get_author()): ?>
                    <li>
                        <form class='link' action='comment/edit' method='post'>
                            <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>
                            <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- si l'utilisateur est proprio du tableau ou si l'utilisateur est l'auteur du message -->
                    <?php if($user == $board->get_owner() || $user == $comment->get_author()): ?>
                    <li>
                        <form class='link' action='comment/delete' method='post'>
                            <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>
                            <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endforeach ?>
        </ul>
    </div>
    <footer>
        <form class="add" action="comment/add" method="post">
            <input type="text" name="title">
            <input type="submit" value="Add a comment">
        </form>
    </footer>
</section>