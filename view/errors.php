<div class="notification is-danger is-light mb-4">
    <ul>
    <?php foreach ($errors->get_messages() as $message): ?>
        <li><?= $message; ?></li>
    <?php endforeach; ?>
    </ul>
</div>    