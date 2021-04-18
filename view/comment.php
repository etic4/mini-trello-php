<li class="is-flex is-flex-direction-row mb-1 is-align-items-baseline">
    <span class="icon">
        <i class="far fa-comment"></i>
    </span>
    <span class="mr-1"><?= $comment->get_body() ?></span>
    <span class="mr-1">by <strong class="has-text-info"><?= $comment->get_author_fullName() ?></strong> </span>
    <span><?= ViewUtils::most_recent_interval($comment) ?>.</span>

    <!-- si l'utilisateur est admin ou l'auteur du message -->
    <?php if( $user->is_admin() || $user->is_author($comment)): ?>
    <a class="icon  ml-4" href="comment/edit/<?= $comment->get_id() ?>/<?= str_replace("/", "_", $redirect_url) ?>">
        <button class="button align-baseline is-white p-0" type="submit">
            <i class="fas fa-edit"></i>
        </button>
    </a>
    <?php endif; ?>

    <!-- si l'utilisateur est proprio du tableau ou si l'utilisateur est l'auteur du message -->
    <?php if($user->can_delete_comment($comment)): ?>
    <form action='comment/delete' method='post'>
        <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>
        <input type="test" name="redirect_url" value="<?= $redirect_url ?>" hidden>
        <button class="button align-baseline is-white p-0 ml-2" type="submit">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
    <?php endif; ?>
</li>

