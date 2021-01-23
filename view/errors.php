<div class='errors'>
    <ul>
    <?php foreach ($errors->get_messages() as $message): ?>
        <li><?= $message; ?></li>
    <?php endforeach; ?>
    </ul>
</div>    