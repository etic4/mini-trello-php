<!DOCTYPE html>
<html lang="fr">
<?php include('html_head.php'); ?>

<body class="edit">
        <header id="main_header">
            <?php include('menu.php') ?>
        </header>
        <?= $breadcrumb->get_trace(); ?>
        <main>
            <article id="editCard">
                <header>
                    <h2>Edit a card</h2>
                    <p class="credit">Created <?= ViewUtils::created_intvl($card) ?> by <strong>'<?= $card->get_author_fullName() ?>'</strong>. <?= ViewUtils::modified_intvl($card) ?>.</p>
                </header>
                <div class="main_card">
                <?php if ($errors->has_errors()): ?>
                    <?php include('errors.php'); ?>
                <?php endif; ?>
                    <form id="edit-card" action="card/update" method="post"></form>
                        <div>
                            <label for="title" >Title</label>
                            <!-- value renvoie la valeur de départ si user ne modifie pas le titre -->
                            <input form="edit-card" type="text" name="title" id="title" maxlength="128" value='<?= $card->get_title() ?>' placeholder='<?= $card->get_title() ?>'>
                        </div>
                        <div>
                            <label for="body">Body</label>
                            <textarea form="edit-card" name="body" id="body" rows="10"><?= $card->get_body() ?></textarea>
                        </div>
                        <div>
                            <label for="board">Board</label>
                            <input form="edit-card" type ="text" name="title_board" id="title_board" value='<?= $card->get_board_title() ?>' disabled>
                        </div>

                        <div class="edit-due-date">
                            <label for="due-date">Due Date</label>
                            <input form="edit-card" type="date" id="start" name="due_date" min="<?=ViewUtils::date_picker_min_due_date($card)?>" value="<?=ViewUtils::date_picker_due_date($card->get_dueDate())?>">

                        </div>

                        <div class="flex-column">
                            <?php if ($card->has_participants()): ?>
                            <label for="participants-remove">Current Participant(s)</label>
                            <ul style="list-style-type: disc;margin-left: 20px;">
                                <?php foreach ($card->get_participants() as $participant): ?>
                                <li class="flex-row"  style="color:#0029CC; align-items: center;"><?=$participant?>
                                    <form id="participants-remove" action="participant/remove"  method="post">
                                        <input type='text' name='id' value='<?= $participant->get_id() ?>' hidden>
                                        <input type="text" name="card_id" value="<?=$card->get_id()?>" hidden>
                                        <input type='submit' value="&#xf2ed" class="fas fa-trash" style="background:none; color:black; border:none;">
                                    </form>
                                </li>
                                <?php endforeach; ?>

                            </ul>
                            <?php else: ?>
                            <label for="participants">This card has no participants yet</label>
                            <?php endif;?>
                        </div>

                        <?php if ($card->has_collabs_no_participating()): ?>
                        <div>
                            <label for="participants-add">Add a participant</label>

                            <div class="flex-row">
                                <form id="participants-add" action="participant/add" method="post">
                                    <select name="id" id="participants-select">
                                        <?php foreach ($card->get_collaborators($participating=false) as $participant): ?>
                                            <option value="<?=$participant->get_id()?>"><?=$participant?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="card_id" value="<?=$card->get_id()?>" hidden>
                                    <input type="submit" class="fas fa-plus" aria-hidden="true" value="">
                                </form>

                            </div>

                        </div>
                        <?php endif; ?>

                        <div>
                            <label for="title_column">Column</label>
                            <input form="edit-card" type ="text" name="title_column" id="title_column" value='<?= $card->get_column_title() ?>' disabled>
                        </div>
                        <div>
                            <input form="edit-card" type="text" name="id" value='<?= $card->get_id() ?>' hidden>
                            <input form="edit-card" type="submit" value="Cancel" name="edit">
                            <input form="edit-card" type="submit" value="Edit this card" name="edit">
                        </div>
                    </form>
                </div>
                <?php include('view_comments.php') ?>
            </article>
        </main>
    </body>
</html>