<?php if ($card->has_participants()): ?>
<section>
    <p class="title is-4 mb-2">Current Participant(s)</p>
    <ul class="ml-2 mb-4">
        <?php foreach ($card->get_participants() as $participant): ?>
            <?php include("participant.php") ?>
        <?php endforeach; ?>
    </ul>
</section>
<?php else: ?>
<section>
    <div class="mb-2">
        <p>This card has no participants yet</p>
    </div>
</section>
<?php endif;?>
