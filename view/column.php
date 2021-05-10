<div class="trello-column card mr-2 p-3 has-background-grey-lighter" data-column-id="<?= $column->get_id() ?>" >
    <div class="column-droppable">
        <div class="trello-column-head is-flex-direction-row is-align-items-baseline p-2 mb-2 title is-5">
            <b class="text-overflow-ellipsis"><?= $column->get_title() ?></b>

            <div class="ml-2 is-flex is-flex-direction-row">
                <a class="icon ml-1" href="column/edit/<?= $column->get_id() ?>">
                    <button class="button align-baseline has-background-grey-lighter p-1" type="submit">
                        <i class="fas fa-edit"></i>
                    </button>
                </a>
                <form id="column-delete-form" class="ml-1" action='column/delete' method='post'>
                    <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                    <input type='text' name='confirm' hidden>
                    <button id="column-delete" class="button align-baseline has-background-grey-lighter is-align-items-start p-0" type="submit">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>

                <?php if(!$column->is_first()): ?>
                <form class="move-arrow ml-1" action='column/left' method='post'>
                    <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                    <button class="button align-baseline has-background-grey-lighter is-align-items-start p-0" type="submit">
                        <i class="fa fa-arrow-circle-left"></i>
                    </button>
                </form>
                <?php endif; ?>

                <?php if(!$column->is_last()): ?>
                <form class="move-arrow ml-1" action='column/right' method='post'>
                    <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                    <button class="button align-baseline has-background-grey-lighter is-align-items-start p-0" type="submit">
                        <i class="fa fa-arrow-circle-right"></i>
                    </button>
                </form>
                <?php endif; ?>

                <?php if ($errors->has_errors("column", "edit", $column->get_id())): ?>
                    <?php include('errors.php'); ?>
                <?php endif; ?>
            </div>
        </div>

        <?php foreach($column->get_cards() as $card): ?>
            <?php include("card.php"); ?>
        <?php endforeach; ?>
    </div>
    <div class="is-flex is-flex-direction-column">
        <form class="card-add" action="card/add" method="post">
            <input id="board-id" type='text' name='board_id' value='<?= $board->get_id() ?>' hidden>
            <input id="column-id" type='text' name='column_id' value='<?= $column->get_id() ?>' hidden>
            <div class="field has-addons">
                <div class="control">
                    <input id="card-title" class="input" type="text" name="card_title" placeholder="Add a card">
                </div>
                <div class="control">
                    <button type="submit" class="button align-baseline is-info">
                        <i class="fas fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </form>
        <?php if ($errors->has_errors("card", "add", $column->get_id())): ?>
            <?php include('errors.php'); ?>
        <?php endif; ?>
    </div>
</div>
