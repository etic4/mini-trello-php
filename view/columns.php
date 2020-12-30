<ul class="column_display">
    <?php foreach($board->get_columns() as $column): ?>
    <li>
        <section class="column">
            <header class="title_column">
                <ul class="icons">
                    <li>
                        <form class='editTitle' action='column/edit' method='post'>
                            <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                            <input type ="checkbox" id="toggle">
                            <label for="toggle"><i class="fas fa-edit"></i></label>
                            <input class="control" type="text" name="title" value="<?= $column->get_title() ?>">
                            <input class="fas fa-paper-plane" type="submit" value="&#xf1d8">
                            <button class="control"><i class="fas fa-undo-alt"></i></button>
                            <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
                            <h3><?= $column->get_title() ?></h3>
                        </form>
                    </li>
                    <li>
                        <form class='link' action='column/delete' method='post'>
                            <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                            <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                        </form>
                    </li>
                    <!-- pas de left pour la première colonne -->
                    <?php if($column->get_position() > 0): ?>
                    <li>
                        <form class='link' action='column/left' method='post'>
                            <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                            <input type='submit' value="&#xf0a8" class="fas fa-arrow-circle-left" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de right pour la dernière colonne -->
                    <?php if($column->get_position() != end($columns)->get_position()): ?>
                    <li>
                        <form class='link' action='column/right' method='post'>
                            <input type='text' name='id' value='<?= $column->get_id() ?>' hidden>
                            <input type='submit' value="&#xf0a9" class="fas fa-arrow-circle-right" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                </ul>
            </header>
            <section>
                <?php include("cards.php"); ?>
            </section>
            <footer>   
                <form class="add" action="card/add" method="post">
                    <input type='text' name='board_id' value='<?= $board->get_id() ?>' hidden>
                    <input type='text' name='column_id' value='<?= $column->get_id() ?>' hidden>
                    <input type="text" name="title" placeholder="Add a card">
                    <input type="submit" value="&#xf067" class="fas fa-plus">
                </form>
            </footer>
        </section>
    </li>
    <?php endforeach; ?>
</ul>