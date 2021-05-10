<div class="modal is-active has-text-danger has-text-centered">
    <div class="modal-content">
        <div class="icon is-very-large">
            <i class="fas fa-trash-alt fa-4x"></i>
        </div>

        <hr>
        <p class="mb-2">Do you really want to delete this collaborator ?</p>
        <p class="mb-4">She/he has participations in <?= $part_count ?> cards in this board</p>
        <p class="mb-4">This process cannot be undone.</p>

        <div class="is-flex is-justify-content-center">
            <a class="button is-light" href="<?= $cancel_url ?>">Cancel</a>
            <form action="collaboration/remove" method="post">
                <input type="text" name="collab_id" value=<?= $collab_id ?> hidden>
                <input type="text" name="board_id" value=<?= $board_id ?> hidden>
                <input type="text" name="confirm" value="true" hidden>
                <input class="button is-danger ml-3" type='submit' value='Remove' name='remove'>
            </form>
        </div>
    </div>
</div>