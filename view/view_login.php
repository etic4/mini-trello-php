<!DOCTYPE html>
<html lang="fr">
<?php $title = "Login"; include('head.php'); ?>

<body  class="has-navbar-fixed-top m-4">
        <header id="main_header">
            <?php include('menu.php'); ?>
        </header>
        <main>
            <section>
                <div class="container card has-text-centered p-5 mw-400 border">
                    <h2 class="title">Login</h2>
                    <hr>

                    <?php if ($errors->has_errors()): ?>
                        <?php include('errors.php'); ?>
                    <?php endif; ?>

                    <form class="mt-5" action="user/login" method="post">
                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input" type="email" name="email" placeholder="Email" value="<?= $email ?>">
                                <span class="icon is-small is-left">
                                  <i class="fas fa-at"></i>
                                </span>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input" type="password" name="password" placeholder="Password" value="<?= $password ?>">
                                <span class="icon is-small is-left">
                                  <i class="fas fa-lock"></i>
                                </span>
                            </div>
                        </div>

                        <div class="control mt-5">
                            <input class="button is-link expand-button" type="submit" value="Sign up">
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </body>
</html>