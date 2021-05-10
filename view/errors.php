<div class="notification is-danger is-light mt-1">
    <ul>
    <?php foreach ($errors->get_messages() as $message): ?>
        <li><?= $message; ?></li>
    <?php endforeach; ?>
    </ul>
</div>    