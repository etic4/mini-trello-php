<li class="is-flex is-flex-direction-row mb-1 is-align-items-baseline">
    <span class="icon">
        <i class="far fa-user"></i>
    </span>
    <span class="has-text-info">
        <strong class="has-text-info"><?=$participant->get_fullName() ?></strong><?= " (".$participant->get_email().")" ?>
    </span>

    <!--si redirect est set c'est qu'on peut supprimer-->
    <?php if(isset($redirect_url)): ?>
    <form action='participant/remove' method='post'>
        <input type='text' name='participant_id' value='<?= $participant->get_id() ?>' hidden>
        <input type='text' name='card_id' value='<?= $card->get_id() ?>' hidden>
        <button class="button align-baseline is-white p-0 ml-4" type="submit">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
    <?php endif; ?>
</li>