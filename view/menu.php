<div class="menu">
    <div class="left">
        <h1>Trello!</h1>
    </div>
    <div class="right">
        <?php if (isset($user) && $user instanceof User): ?>

            <?php if($user->is_admin()): ?>
                <span class="menu-item"><a href="board/index">Boards</a></span>
                <span class="menu-item"><a href="user/manage">Admin</a></span>
                <p id="logged-user"><i class="fas fa-user-shield no-pointer"></i><?= $user->get_fullName() ?></p>

                <?php else: ?>
                <p id="logged-user"><i class="fas fa-user no-pointer"></i><?= $user->get_fullName() ?></p>
            <?php endif; ?>
            <p><a href="user/logout"><i class="fas fa-sign-out-alt"></i></a></p>
        <?php else: ?>
            <p><a class="loginLink" href="user/login"><i class="fas fa-sign-in-alt"></i></a></p>
            <p><a class="signupLink" href="user/signup"><i class="fas fa-user-plus"></i></a></p>
        <?php endif;?>
    </div>
</div>
