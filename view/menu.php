<nav class="navbar is-fixed-top is-info" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item is-size-3" href="#">
            Trello
        </a>
    </div>

    <div id="navbar" class="navbar-menu">
        <div class="navbar-end">
            <a class="navbar-item" href="board/index">Boards</a>
            <div id="calendar-menu"></div>
            <?php if (isset($user) && $user != null): ?>
                <?php if($user->is_admin()): ?>
            <a class="navbar-item" href="user/manage">Admin</a>
            <div class="navbar-item">
                <span class="icon-text">
                    <span class="icon">
                        <i class="fas fa-user-shield no-pointer"></i>
                    </span>
                    <span><?= $user->get_fullName() ?></span>
                </span>

            </div>
                <?php else: ?>
            <div class="navbar-item">
                <span class="icon-text">
                    <span class="icon"><i class="fas fa-user no-pointer"></i></span>
                    <span><?= $user->get_fullName() ?></span>
                </span>
            </div>
                <?php endif; ?>
            <a class="navbar-item" href="user/logout">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
            </a>

            <?php else: ?>
                <a class="navbar-item" href="user/login">
                    <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
                </a>
                <a class="navbar-item" href="user/signup">
                    <span class="icon"><i class="fas fa-user-plus"></i></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
