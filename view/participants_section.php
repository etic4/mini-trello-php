<section id="participants" class="mb-5">
<?php if ($card->has_participants()): ?>
    <div class=" mb-5">
        <p class="title is-4 mb-2">Current Participant(s)</p>
        <ul class="ml-2">
            <?php foreach ($card->get_participants() as $participant): ?>
                <?php include("participant.php") ?>
            <?php endforeach; ?>
        </ul>
    </div>

<?php else: ?>
    <div class="mb-5">
        <p><b>This card has no participants yet</b></p>
    </div>
<?php endif;?>

<?php if (isset($redirect_url) && $card->has_collabs_no_participating()): ?>
    <div>
        <p class="title is-5 mb-2">Add a new participant</p>
        <form class="add" action="participant/add" method="post">
            <input type="text" name="card_id" value="<?= $card->get_id() ?>" hidden>
            <input type="text" name="redirect_url" value="<?= $redirect_url?>" hidden>

            <div class="field has-addons">
                <div class="control">
                    <div class="select" >
                        <select name="participant_id" id="others">
                            <?php foreach ($card->get_collaborators($participating=false) as $participant): ?>
                                <option value="<?=$participant->get_id()?>"><?=$participant?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="control">
                    <button type="submit" class="button is-info">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>
</section>