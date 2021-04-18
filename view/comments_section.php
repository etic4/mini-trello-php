<section id="comments">
<?php if($card->has_comments()): ?>
    <div class="mb-4">
        <p class="title is-4 mb-2">Comments</p>
        <ul class="ml-2 mb-4">
            <?php foreach($card->get_comments() as $comment):?>
                <?php include('comment.php'); ?>
            <?php endforeach ?>
        </ul>
    </div>
<?php else: ?>
    <div class="mb-5">
        <p><b>This card has no comments yet</b></p>
    </div>
<?php endif; ?>
    <div class="columns">
        <div class="column is-half">
            <form class="" action="comment/add" method="post">
                <input type="text" name="card_id" value="<?= $card->get_id() ?>" hidden>
                <input type="test" name="redirect_url" value="<?= $redirect_url ?>" hidden>
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