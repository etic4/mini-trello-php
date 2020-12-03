<!-- code php
boucle foreach -> 1 card = 1 section  class card -->
<ul class="display_cards">
    <li>
        <section class="card">
            <header>
                <h4>code php titre card</h4>
            </header>
            <footer>
                <!-- code php
                pas de left pour les cartes de la première colonne
                pas de right pour les cartes de la dernière colonne
                pas de up pour la première carte de la colonne
                pas de down pour la dernière carte de la colonne -->
                <!-- <input type='text' name='id_card' value='<?= $card->id ?>' hidden> -->
                <ul class="icons">
                    <li>
                        <form class='link' action='card/view' method='post'>
                            <input type='text' name='id_card' value='php' hidden>
                            <input type='submit' value="&#xf06e" class="far fa-eye">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='card/edit' method='post'>
                            <input type='text' name='id_card' value='php' hidden>
                            <input type='submit' value="&#xf044"class="fas fa-edit">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='card/delete' method='post'>
                            <input type='text' name='id_card' value='php' hidden>
                            <input type='submit' value="&#xf2ed" class="far fa-trash-alt">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='card/down' method='post'>
                            <input type='text' name='id_card' value='php' hidden>
                            <input type='submit' value="&#xf0ab" class="fas fa-arrow-circle-down">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='card/up' method='post'>
                            <input type='text' name='id_card' value='php' hidden>
                            <input type='submit' value="&#xf0aa" class="fas fa-arrow-circle-up">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='card/left' method='post'>
                            <input type='text' name='id_card' value='php' hidden>
                            <input type='submit' value="&#xf0a8" class="fas fa-arrow-circle-left">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='card/left' method='post'>
                            <input type='text' name='id_card' value='php' hidden>
                            <input type='submit' value="&#xf0a9" class="fas fa-arrow-circle-right">
                        </form>
                    </li>
                </ul>
            </footer> 
        </section>
    </li>
</ul>