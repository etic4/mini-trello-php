<!---->
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
        <title>Login</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body class="login">
        <header id="main_header">
            <?php include('menu.php'); ?>
        </header>
        <main>
            <form id="main_form" action="user/login" method="post">
                <h2>Sign in</h2>
                <hr>
                <?php if ($errors->has_errors()): ?>
                    <?php include('errors.php'); ?>
                <?php endif; ?>
                <ul class="wrapper">
                    <li class="form-row">
                        <label for="email"><i class="fas fa-user"></i></label>
                        <input type="email" name="email" placeholder="Email" value="<?=$email?>">
                    </li>
                    <li class="form-row">
                        <label for="password"><i class="fas fa-lock"></i></label>
                        <input type="password" name="password" placeholder="Password" value="<?=$password?>">
                    </li>
                    <li class="form-row">
                        <input type="submit" value="Login">
                    </li>
                </ul>
                
            </form>
            
        </main>
    </body>
</html>