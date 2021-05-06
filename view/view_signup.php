<!DOCTYPE html>
<html lang="fr">
<head>
    <?php $title="Signup"; include('head.php'); ?>
    <script src = "lib/js/validation.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            setup_signup_validation();
        })
    </script>
</head>

    <body class="has-navbar-fixed-top m-4">
<!--        <script src="lib/js/signup-validation.js" type="text/javascript"></script>-->

        <header>
            <?php include('menu.php'); ?>
        </header>
        <main>
            <section>
                <div class="container card has-text-centered p-5 mw-400 border">
                    <h2 class="title">Sign up</h2>
                    <hr>

                    <?php if ($errors->has_errors()): ?>
                        <?php include('errors.php'); ?>
                    <?php endif; ?>

                    <form id="signup" class="mt-5" action="user/signup" method="post">
                        <div class="field">
                            <div class="control has-icons-left">
                                <input id="email" class="input" type="email" name="email" placeholder="Email" value="<?= $email ?>">
                                <span class="icon is-small is-left">
                                  <i class="fas fa-at"></i>
                                </span>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="fullName" placeholder="Full Name" value="<?= $fullName ?>">
                                <span class="icon is-small is-left">
                                  <i class="fas fa-user"></i>
                                </span>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control has-icons-left">
                                <input id="password" class="input" type="password" name="password" placeholder="Password" value="<?= $password ?>">
                                <span class="icon is-small is-left">
                                  <i class="fas fa-lock"></i>
                                </span>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input" type="password" name="confirm" placeholder="Confirm your password" value="<?= $confirm ?>">
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