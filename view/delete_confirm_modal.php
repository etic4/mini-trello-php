<div class="modal is-active has-text-danger has-text-centered">
    <div class="modal-content">
        <div class="icon is-very-large">
            <i class="fas fa-trash-alt fa-4x"></i>
        </div>
        <hr>
        <p class="mb-2">Do you really want to delete this <?= ViewUtils::class_name($instance) ?> ?</p>
        <p class="mb-4">This process cannot be undone.</p>
        <div class="is-flex is-justify-content-center">
            <a class="button is-info" href="<?= $cancel_url ?> ">Cancel</a>
            <form  id="main_form" action="<?= ViewUtils::class_name($instance) ?>/delete" method="post">
                <input type="text" name="id" value=<?= $instance->get_id()?> hidden>
                <input type="text" name="confirm" value="true" hidden>
                <input class="button is-danger ml-3" type='submit' value='Delete' name='delete'>
            </form>
        </div>
    </div>
</div>