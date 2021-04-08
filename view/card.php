<?php extract(ViewUtils::due_date_styling($card)); ?>

<div class="card mb-2 p-2 pl-4 <?= $card_background ?>">
    <div class="pt-3 pb-3">
        <p class="<?= $text_color ?> is-6 text-overflow-ellipsis"><b><?= $card->get_title() ?></b></p>
    </div>

    <footer class="is-flex is-flex-direction-row is-align-items-end">
        <a class="button <?= $button_background ?> p-1" href="card/view/<?= $card->get_id() ?>">
            <i class="fas fa-eye"></i>
        </a>

        <?php if($card->has_comments()): ?>
            <div class="trello-comment button <?= $button_background ?> p-0">
                <?= $card->get_comments_count() ?>
                <i class="far fa-comment ml-1"></i>
            </div>
        <?php endif; ?>

        <a class="button <?= $button_background ?> p-1 pl-0" href="card/edit/<?= $card->get_id() ?>">
            <i class="fas fa-edit"></i>
        </a>

        <form class="icon" action='card/delete' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button <?= $button_background ?> p-0" type="submit">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>

        <?php if(!$card->is_last()): ?>
        <form class="icon" action='card/down' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button <?= $button_background ?> p-0" type="submit">
                <i class="fas fa-arrow-circle-down"></i>
            </button>
        </form>
        <?php endif; ?>

        <!-- pas de up pour la première carte de la colonne -->
        <?php if(!$card->is_first()): ?>
        <form class="icon" action='card/up' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button <?= $button_background ?> p-0" type="submit">
                <i class="fas fa-arrow-circle-up"></i>
            </button>
        </form>
        <?php endif; ?>

        <!-- pas de left pour les cartes de la première colonne -->
        <?php if(!$column->is_first()): ?>
        <form class='icon' action='card/left' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button <?= $button_background ?> p-0" type="submit">
                <i class="fas fa-arrow-circle-left"></i>
            </button>
        </form>
        <?php endif; ?>

        <!-- pas de right pour les cartes de la dernière colonne -->
        <?php if(!$column->is_last()): ?>
        <form class='icon' action='card/right' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button <?= $button_background ?> p-0" type="submit">
                <i class="fas fa-arrow-circle-right"></i>
            </button>
        </form>
        <?php endif; ?>
    </footer>
</div>

