<div class="card mr-2 p-3 has-background-grey-lighter">
    <div class="p-2 is-flex is-flex-direction-row is-align-items-baseline">
        <b><?= ViewUtils::truncate_string($column->get_title(), 28) ?></b>
        <div class="ml-2 is-flex is-flex-direction-row">
            <form class="mr-1" action='column/edit' method='post'>
                <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                <button class="button has-background-grey-lighter is-align-items-start p-0" type="submit">
                    <i class="fas fa-edit" aria-hidden="true"></i>
                </button>
            </form>
            <form class="mr-1" action='column/delete' method='post'>
                <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                <button class="button has-background-grey-lighter is-align-items-start p-0" type="submit">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
            <?php if(!$column->is_first()): ?>
            <form class="mr-1" action='column/left' method='post'>
                <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                <button class="button has-background-grey-lighter is-align-items-start p-0" type="submit">
                    <i class="fa fa-arrow-circle-left"></i>
                </button>
                </form>
            <?php endif; ?>
            <!-- pas de right pour la dernière colonne -->
            <?php if(!$column->is_last()): ?>
            <form class="mr-1" action='column/right' method='post'>
                <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                <button class="button has-background-grey-lighter is-align-items-start p-0" type="submit">
                    <i class="fa fa-arrow-circle-right"></i>
                </button>
            </form>
            <?php endif; ?>
            <?php if ($errors->has_errors("column", "edit", $column->get_id())): ?>
                <?php include('errors.php'); ?>
            <?php endif; ?>
        </div>

    </div>
    <div>
        <?php foreach($column->get_cards() as $card): ?>
            <?php include("card.php"); ?>
        <?php endforeach; ?>
    </div>
    <div class="is-flex is-flex-direction-column">
        <form class="add" action="card/add" method="post">
            <input type='text' name='board_id' value='<?= $board->get_id() ?>' hidden>
            <input type='text' name='column_id' value='<?= $column->get_id() ?>' hidden>
            <div class="field has-addons">
                <div class="control">
                    <input class="input" type="text" name="title" placeholder="Add a card">
                </div>
                <div class="control">
                    <button type="submit" class="button is-info"><i class="fas fas fa-plus"></i></button>
                </div>
            </div>
        </form>
        <?php if ($errors->has_errors("card", "add", $column->get_id())): ?>
            <?php include('errors.php'); ?>
        <?php endif; ?>
    </div>
</div>
