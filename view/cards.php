<!-- code php
boucle foreach -> 1 card = 1 section  class card -->
<ul class="display_cards">
    <li>
        <section class="card">
            <header>
                <h4>code php titre card</h4>
            </header>
            <footer>
                <ul class="icons">
                    <li>
                        <form class='link' action='card/view' method='post'>
                            <input type='text' name='id' value='<?= $card->id ?>' hidden>
                            <input type='submit' value="&#xf06e" class="far fa-eye" style="background:none">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='card/edit' method='post'>
                            <input type='text' name='id' value='<?= $card->id ?>' hidden>
                            <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='card/delete' method='post'>
                            <input type='text' name='id' value='<?= $card->id ?>' hidden>
                            <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                        </form>
                    </li>
                    <!-- pas de down pour la dernière carte de la colonne -->
                    <?php if($card->position != end($cards)->position): ?>
                    <li>
                        <form class='link' action='card/down' method='post'>
                            <input type='text' name='id' value='<?= $card->id ?>' hidden>
                            <input type='submit' value="&#xf0ab" class="fas fa-arrow-circle-down" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de up pour la première carte de la colonne -->
                    <?php if($card->position > 0): ?>
                    <li>
                        <form class='link' action='card/up' method='post'>
                            <input type='text' name='id' value='<?= $card->id ?>' hidden>
                            <input type='submit' value="&#xf0aa" class="fas fa-arrow-circle-up" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de left pour les cartes de la première colonne -->
                    <?php if($column->position != end($columns)->position): ?>
                    <li>
                        <form class='link' action='card/left' method='post'>
                            <input type='text' name='id' value='<?= $card->id ?>' hidden>
                            <input type='submit' value="&#xf0a8" class="fas fa-arrow-circle-left" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                    <!-- pas de right pour les cartes de la dernière colonne -->
                    <?php if($column->position > 0): ?>
                    <li>
                        <form class='link' action='card/right' method='post'>
                            <input type='text' name='id' value='<?= $card->id ?>' hidden>
                            <input type='submit' value="&#xf0a9" class="fas fa-arrow-circle-right" style="background:none">
                        </form>
                    </li>
                    <?php endif; ?>
                </ul>
            </footer> 
        </section>
    </li>
</ul>