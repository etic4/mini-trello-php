<ul class="display_cards">
 <?php foreach($column->get_cards() as $card): ?>
    <li>
        <section class="card">
            <header>
                <h4><?= $card->get_title() ?></h4>
            </header>
            <footer>
                <ul class="icons">
                    <li>
                        <a href="card/view/<?= $card->get_id() ?>"><i class="far fa-eye"></i></a>
                    </li>
                    <?php if(count($card->get_comments()) > 0): ?>
                    <li>
                        <p class='button_comment'>&#x28;<?= count($card->get_comments()) ?>&nbsp;<i class="far fa-comment"></i>&#x29;</p>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="card/edit/<?= $card->get_id() ?>"><i class="fas fa-edit"></i></a>
                    </li>
                    <li>
                        <form class='link' action='card/delete_confirm' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                        </form>
                    </li>
                    <!-- pas de down pour la dernière carte de la colonne -->
                    <?php if($card->get_position() < Card::get_cards_count($column) - 1): ?>
                    <li>
                        <form class='link' action='card/down' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='submit' value="&#xf0ab" class="fas fa-arrow-circle-down" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de up pour la première carte de la colonne -->
                    <?php if($card->get_position() > 0): ?>
                    <li>
                        <form class='link' action='card/up' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='submit' value="&#xf0aa" class="fas fa-arrow-circle-up" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de left pour les cartes de la première colonne -->
                    <?php if($column->get_position() > 0): ?>
                    <li>
                        <form class='link' action='card/left' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='submit' value="&#xf0a8" class="fas fa-arrow-circle-left" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de right pour les cartes de la dernière colonne -->
                    <?php if($column->get_position() < Column::get_columns_count($board)-1): ?>
                    <li>
                        <form class='link' action='card/right' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='submit' value="&#xf0a9" class="fas fa-arrow-circle-right" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                </ul>
            </footer> 
        </section>
    </li>
    <?php endforeach; ?>
</ul>