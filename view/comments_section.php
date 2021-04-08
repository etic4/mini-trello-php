<?php if($card->has_comments()): ?>
    <section>
        <p class="title is-4 mb-2">Comments</p>
        <ul class="ml-2 mb-4">
            <?php foreach($card->get_comments() as $comment):?>
                <?php include('comment.php'); ?>
            <?php endforeach ?>
        </ul>
        <div class="columns">
            <div class="column is-half">
                <form class="" action="comment/add" method="post">
                    <input type='text' name='card_id' value='<?= $card->get_id() ?>' hidden>
                    <div class="field has-addons">
                        <div class="control is-expanded">
                            <input class="input" type="text" name="body" placeholder="Add a comment">
                        </div>
                        <div class="control">
                            <button type="submit" class="button is-info">Add a comment</i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php else: ?>
    <section>
        <div class="mb-2">
            <p>This card has no comments yet</p>
        </div>
    </section>
<?php endif; ?>
