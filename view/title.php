<div class="title">
    <!-- <h2>Board "<?= $board->title ?>"</h2> -->
    <!-- OU -->
    <!-- <h2>Card "<?= $card->title ?>"</h2> -->
    <h2>Board ''code php titre''</h2>
    <!-- code php
    id pour delete et edit (?) -->
    <!-- <form class='link' action='board/edit' method='post'>
            <input type='text' name='id_board' value='<?= $board->id ?>' hidden> -->
    <!-- OU -->
    <!-- <form class='link' action='card/edit' method='post'>
            <input type='text' name='id_card' value='<?= $card->id ?>' hidden> -->
    <!-- idem pour delete --> 
    <ul class="icons">
        <li>
            <form class='link' action='board/edit' method='post'>
                <input type='text' name='id' value='<?= $board->id ?>' hidden>
                <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
            </form>
        </li>
        <li>
            <form class='link' action='board/delete' method='post'>
                <input type='text' name='id' value='<?= $board->id ?>' hidden>
                <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
            </form>
        </li>
    </ul>
</div>
<p class="credit">Created 'code php time' ago by <strong>'code php fullname'</strong>. 'code php modified'</p>