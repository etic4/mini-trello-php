<li class="is-flex is-flex-direction-row mb-2 p-1">
    <span class="icon">
        <i class="far fa-user"></i>
    </span>
    <span class="has-text-info">
        <strong class="has-text-info"><?=$collaborator->get_fullName() ?></strong><?= " (".$collaborator->get_email().")" ?>
    </span>
    <form id="remove-collab-form" action='collaboration/remove' method='post'>
        <input type='text' name='collab_id' value='<?= $collaborator->get_id() ?>' hidden>
        <input type='text' name='board_id' value='<?= $board->get_id() ?>' hidden>
        <input type='text' name='confirm' hidden>
        <button id="collab-delete" class="button align-baseline is-white p-0 ml-4" type="submit">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
</li>
