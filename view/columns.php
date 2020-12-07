<ul class="column_display">
    <?php foreach($columns as $column): ?>
    <li>
        <section class="column">
            <header class="title_column">
                <h3><?= $column->title ?></h3>
                <ul class="icons">
                    <li>
                        <form class='link' action='column/edit' method='post'>
                            <input type='text' name='id' value='<?= $column->id ?>' hidden>
                            <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='column/delete' method='post'>
                            <input type='text' name='id' value='<?= $column->id ?>' hidden>
                            <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                        </form>
                    </li>
                    <!-- pas de left pour la première colonne -->
                    <?php if($column->position > 0): ?>
                    <li>
                        <form class='link' action='column/left' method='post'>
                            <input type='text' name='id' value='<?= $column->id ?>' hidden>
                            <input type='submit' value="&#xf0a8" class="fas fa-arrow-circle-left" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de right pour la dernière colonne -->
                    <?php if($column->position != end($columns)->position): ?>
                    <li>
                        <form class='link' action='card/right' method='post'>
                            <input type='text' name='id' value='<?= $column->id ?>' hidden>
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
                    <input type="text" name="title" placeholder="Add a card">
                    <input type="submit" value="&#xf067" class="fas fa-plus">
                </form>
            </footer>
        </section>
    </li>
    <?php endforeach; ?>
</ul>