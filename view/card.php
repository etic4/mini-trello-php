<?php extract(ViewUtils::due_date_styling($card)); ?>

<div class="card mb-2 p-2 pl-4 <?= $card_background ?>">
    <div class="pt-3 pb-3">
        <p class="<?= $text_color ?> is-6 text-overflow-ellipsis"><b><?= $card->get_title() ?></b></p>
    </div>

    <footer class="is-flex is-flex-direction-row is-align-items-end">
        <a class="button align-baseline <?= $button_background ?> p-1" href="card/view/<?= $card->get_id() ?>">
            <i class="fas fa-eye"></i>
        </a>

        <?php if($card->has_comments()): ?>
            <div class="trello-comment button <?= $button_background ?> p-0">
                <?= $card->get_comments_count() ?>
                <i class="far fa-comment ml-1"></i>
            </div>
        <?php endif; ?>

        <form action="card/edit" method="post">
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <input type="text" name="redirect_url" value="board/view/<?= $board->get_id() ?>" hidden>
            <button class="button align-baseline <?= $button_background ?> p-0 ml-1" type="submit">
                <i class="fas fa-edit"></i>
            </button>
        </form>

        <form action='card/delete' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button align-baseline <?= $button_background ?> p-0 ml-1" type="submit">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>

        <?php if(!$card->is_last()): ?>
        <form action='card/down' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button align-baseline <?= $button_background ?> p-0 ml-1" type="submit">
                <i class="fas fa-arrow-circle-down"></i>
            </button>
        </form>
        <?php endif; ?>

        <!-- pas de up pour la première carte de la colonne -->
        <?php if(!$card->is_first()): ?>
        <form action='card/up' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button align-baseline <?= $button_background ?> p-0 ml-1" type="submit">
                <i class="fas fa-arrow-circle-up"></i>
            </button>
        </form>
        <?php endif; ?>

        <!-- pas de left pour les cartes de la première colonne -->
        <?php if(!$column->is_first()): ?>
        <form action='card/left' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button align-baseline <?= $button_background ?> p-0 ml-1" type="submit">
                <i class="fas fa-arrow-circle-left"></i>
            </button>
        </form>
        <?php endif; ?>

        <!-- pas de right pour les cartes de la dernière colonne -->
        <?php if(!$column->is_last()): ?>
        <form action='card/right' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button align-baseline <?= $button_background ?> p-0 ml-1" type="submit">
                <i class="fas fa-arrow-circle-right"></i>
            </button>
        </form>
        <?php endif; ?>
    </footer>
</div>

