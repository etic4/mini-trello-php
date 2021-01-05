<section class=comment>
    <header>
        <h3>Comments</h3>
    </header>
    <div class="main_comment">
        <ul>
            <?php foreach($card->get_comments() as $comment):?>
                <li class="display_one_comment">
                    <?php if(isset($show_comment) && $show_comment == $comment->get_id()): ?>
                        <form class='editconfirm' action="comment/edit_confirm" method="post">
                            <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>
                            <?php if(isset($edit)): ?>
                                <input type='text' name='edit' value='yes' hidden>
                            <?php endif;?>
                            <input type="text" name="body" value='<?= $comment->get_body() ?>' >
                            <input class="fas fa-paper-plane" type="submit" name="validate" value="&#xf1d8">
                            <input class="fas fa-arrow-left" type="submit" name="cancel" value="&#xf060">
                        </form>
                    <?php else: ?>
                        <p><?= $comment->get_body() ?> </p>
                        <p>by <strong><?= $comment->get_author_fullName() ?></strong> <?= $comment->get_time_string() ?></p>
                    <?php endif; ?>
                    <ul class="icons">
                        <!-- si l'utilisateur est l'auteur du message -->
                         <?php if($user->get_id() == $comment->get_author()->get_id() && !isset($show_comment)): ?>
                         <li>
                            <form class='link' action='Comment/edit' method='post'>
                                <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>
                                <?php if(isset($edit)): ?>
                                    <input type='text' name='edit' value='yes' hidden>
                                <?php endif;?>
                                <input type='submit' name='show' value="&#xf044"class="fas fa-edit" style="background:none">
                            </form>
                        </li>
                        <?php endif; ?>
                        <!-- si l'utilisateur est proprio du tableau ou si l'utilisateur est l'auteur du message -->
                        <?php if($user->get_id() == $board->get_owner()->get_id() || $user->get_id() == $comment->get_author()->get_id()): ?>
                            <li>
                            <form class='link' action='Comment/delete' method='post'>
                                <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>
                                <?php if(isset($edit)): ?>
                                    <input type='text' name='edit' value='yes' hidden>
                                <?php endif;?>
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
        <form class="add" action="Comment/add" method="post">
            <input type='text' name='idcard' value='<?= $card->get_id() ?>' hidden>
            <?php if(isset($edit)): ?>
                <input type='text' name='edit' value='yes' hidden>
            <?php endif;?>
            <input type="text" name="body">
            <input type="submit" value="Add a comment">
        </form>
    </footer>
</section>