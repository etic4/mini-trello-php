<!-- code php
    id pour delete et edit (?) -->
    <!-- <form class='link' action='board/edit' method='post'>
            <input type='text' name='id_board' value='<?= $board->id ?>' hidden> -->
    <!-- OU -->
    <!-- <form class='link' action='card/edit' method='post'>
            <input type='text' name='id_card' value='<?= $card->id ?>' hidden> -->
    <!-- OU -->
    <!-- <form class='link' action='comment/edit' method='post'>
            <input type='text' name='id_comment' value='<?= $comment->id ?>' hidden> -->
<!-- idem pour delete --->
    <ul class="icons">
        <li>
            <form class='link' action='xxx/edit' method='post'>
                <input type='text' name='id' value='php' hidden>
                <input type='submit' value="&#xf044"class="fas fa-edit" style="background:none">
            </form>
        </li>
        <li>
            <form class='link' action='xxx/delete' method='post'>
                <input type='text' name='id' value='php' hidden>
                <input type='submit' value="&#xf2ed" class="far fa-trash-alt" style="background:none">
            </form>
        </li>
    </ul>