<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
        <title>Signup</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <header>
            <?php include('menu.php'); ?>
        </header>
        <main class="login">
            <form action="user/login" method="post">
                <h2>Sign up</h2>
                <hr>
                <ul class="wrapper">
                    <li class="form-row">
                        <label for="email"><i class="fas fa-at"></i></label>
                        <input type="email" name="email" placeholder="Email">
                    </li>
                    <li class="form-row">
                        <label for="fullname"><i class="fas fa-user"></i></label>
                        <input type="text" name="fullname" placeholder="Full Name">
                    </li>
                    <li class="form-row">
                        <label for="password"><i class="fas fa-lock"></i></label>
                        <input type="password" name="password" placeholder="Password">
                    </li>
                    <li class="form-row">
                        <label for="confirm"><i class="fas fa-lock"></i></label>
                        <input type="password" name="confirm" placeholder="Confirm your password">
                    </li>
                    <li class="form-row">
                        <input type="submit" value="Sign Up"/>
                    </li>
                </ul>
            </form>
        </main>
    </body>
</html>