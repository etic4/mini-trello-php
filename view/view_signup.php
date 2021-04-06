<!DOCTYPE html>
<html lang="fr">
<?php include('head.php'); ?>

<body class="signup">
        <header id="main_header">
            <?php include('menu.php'); ?>
        </header>
        <main class="login">
            <form id="main_form" action="user/signup" method="post">
                <h2>Sign up</h2>
                <hr> 
                <?php if ($errors->has_errors()): ?>
                    <?php include('errors.php'); ?>
                <?php endif; ?>
                <ul class="wrapper">
                    <li class="form-row">
                        <label for="email"><i class="fas fa-at"></i></label>
                        <input type="email" name="email" placeholder="Email" value="<?= $email ?>">
                    </li>
                    <li class="form-row">
                        <label for="fullname"><i class="fas fa-user"></i></label>
                        <input type="text" name="fullName" placeholder="Full Name" value="<?= $fullName ?>">
                    </li>
                    <li class="form-row">
                        <label for="password"><i class="fas fa-lock"></i></label>
                        <input type="password" name="password" placeholder="Password" value="<?= $password ?>">
                    </li>
                    <li class="form-row">
                        <label for="confirm"><i class="fas fa-lock"></i></label>
                        <input type="password" name="confirm" placeholder="Confirm your password" value="<?= $confirm ?>">
                    </li>
                    <li class="form-row">
                        <input type="submit" value="Sign Up">
                    </li>
                </ul>
            </form>
        </main>
    </body>
</html>