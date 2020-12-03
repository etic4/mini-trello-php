<!-- code php
boucle foreach -> 1 colonne = 1 section class column -->  
<ul class="column_display">
    <li>
        <section class="column">
            <header>
                <h3>code php titre colonne</h3>
                <!-- code php
                pas de left pour la première
                pas de right pour la dernière -->
                <!-- <input type='text' name='id_column' value='<?= $column->id ?>' hidden> -->
                <ul class="icons">
                    <li>
                        <form class='link' action='column/edit' method='post'>
                            <input type='text' name='id_column' value='php' hidden>
                            <input type='submit' value="&#xf044"class="fas fa-edit">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='column/delete' method='post'>
                            <input type='text' name='id_column' value='php' hidden>
                            <input type='submit' value="&#xf2ed" class="far fa-trash-alt">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='column/left' method='post'>
                            <input type='text' name='id_column' value='php' hidden>
                            <input type='submit' value="&#xf0a8" class="fas fa-arrow-circle-left">
                        </form>
                    </li>
                    <li>
                        <form class='link' action='card/right' method='post'>
                            <input type='text' name='id_column' value='php' hidden>
                            <input type='submit' value="&#xf0a9" class="fas fa-arrow-circle-right">
                        </form>
                    </li>
                </ul>
            </header>
            <section>
                <?php include("cards.php"); ?>
            </section>
            <footer>   
                <form class="add" action="card/add" method="post">
                    <input type="text" name="title_card" placeholder="Add a card"/>
                    <input type="submit" value="&#xf067" class="fas fa-plus"/>
                </form>
            </footer>
        </section>
    </li>
</ul>