<li class="is-flex is-flex-direction-row mb-1 is-align-items-baseline">
    <span class="icon">
        <i class="far fa-comment"></i>
    </span>
    <span class="mr-1"><?= $comment->get_body() ?></span>
    <span class="mr-1">by <strong class="has-text-info"><?= $comment->get_author_fullName() ?></strong> </span>
    <span><?= ViewUtils::created_intvl($comment) ?>.</span>

    <!-- si l'utilisateur est admin ou l'auteur du message -->
    <?php if( $user->is_admin() || $user->is_author($comment)): ?>
    <a class="button is-white p-0 ml-2" href="comment/edit/<?= $comment->get_id() ?>">
        <i class="fas fa-edit"></i>
    </a>
    <?php endif; ?>

    <!-- si l'utilisateur est proprio du tableau ou si l'utilisateur est l'auteur du message -->
    <?php if($user->can_delete_comment($comment)): ?>
    <form class='icon' action='comment/delete' method='post'>
        <input type='text' name='id' value='<?= $comment->get_id() ?>' hidden>
        <button class="button is-white p-0" type="submit">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
    <?php endif; ?>
</li>

