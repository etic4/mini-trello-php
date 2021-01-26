<ul class="display_cards">
 <?php foreach($column->get_cards() as $card): ?>
    <li>
        <section class="card">
            <header>
                <h4><?= $card->get_truncated_title(25) ?></h4>
            </header>
            <footer>
                <ul class="icons">
                    <li>
                        <a href="card/view/<?= $card->get_id() ?>"><i class="far fa-eye"></i></a>
                    </li>
                    <?php if($card->has_comments()): ?>
                    <li>
                        <p class='button_comment'>&#x28;<?= $card->get_comments_count() ?>&nbsp;<i class="far fa-comment"></i>&#x29;</p>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="card/edit/<?= $card->get_id() ?>"><i class="fas fa-edit"></i></a>
                    </li>
                    <li>
                        <form class='link' action='card/delete' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                        </form>
                    </li>
                    <!-- pas de down pour la dernière carte de la colonne -->
                    <?php if(!$card->is_last()): ?>
                    <li>
                        <form class='link' action='card/down' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='submit' value="&#xf0ab" class="fas fa-arrow-circle-down" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de up pour la première carte de la colonne -->
                    <?php if(!$card->is_first()): ?>
                    <li>
                        <form class='link' action='card/up' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='submit' value="&#xf0aa" class="fas fa-arrow-circle-up" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de left pour les cartes de la première colonne -->
                    <?php if(!$column->is_first()): ?>
                    <li>
                        <form class='link' action='card/left' method='post'>
                            <input type='text' name='id' value='<?= $card->get_id() ?>' hidden>
                            <input type='submit' value="&#xf0a8" class="fas fa-arrow-circle-left" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de right pour les cartes de la dernière colonne -->
                    <?php if(!$column->is_last()): ?>
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