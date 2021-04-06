<div class="card mb-2 p-2<?= $card->is_due() ? ' has-background-danger': '' ?>">
    <div class="pt-3 pb-3">
        <p class="has-text-info-dark is-6"><b><?= ViewUtils::truncate_string($card->get_title(), 35) ?></b></p>
    </div>

    <footer class="is-flex-direction-row has-text-black">
        <form class="icon" action='card/view' method='get'>
            <button class="button is-white p-0" type="submit">
                <i class="fas fa-eye"></i>
            </button>
        </form>

        <?php if($card->has_comments()): ?>
        <div class="icon">
            <button class="button is-white p-0">
                <?= $card->get_comments_count() ?>
                <i class="fas fa-comment"></i>
            </button>
        </div>

        <?php endif; ?>

        <form class="icon" action="card/edit/<?= $card->get_id() ?>" method='get'>
            <button class="button is-white p-0" type="submit">
                <i class="fas fa-edit"></i>
            </button>
        </form>

<!--        <a class="icon" href="card/edit/--><?//= $card->get_id() ?><!--"><i class="fas fa-edit"></i></a>-->

        <form class="icon" action='card/delete' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button is-white p-0" type="submit">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>

        <?php if(!$card->is_last()): ?>
        <form class="icon" action='card/down' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button is-white p-0" type="submit">
                <i class="fas fa-arrow-circle-down"></i>
            </button>
        </form>
        <?php endif; ?>
        <!-- pas de up pour la première carte de la colonne -->
        <?php if(!$card->is_first()): ?>
        <form class="icon" action='card/up' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button is-white p-0" type="submit">
                <i class="fas fa-arrow-circle-up"></i>
            </button>
        </form>
        <?php endif; ?>
        <!-- pas de left pour les cartes de la première colonne -->
        <?php if(!$column->is_first()): ?>
        <form class='icon' action='card/left' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button is-white p-0" type="submit">
                <i class="fas fa-arrow-circle-left"></i>
            </button>
        </form>
        <?php endif; ?>
        <!-- pas de right pour les cartes de la dernière colonne -->
        <?php if(!$column->is_last()): ?>
        <form class='icon' action='card/right' method='post'>
            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
            <button class="button is-white p-0" type="submit">
                <i class="fas fa-arrow-circle-right"></i>
            </button>
        </form>
        <?php endif; ?>
    </footer>
</div>

